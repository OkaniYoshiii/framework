<?php

namespace OkaniYoshiii\Framework\Commands;

use Exception;
use Nette\PhpGenerator\PhpFile;
use OkaniYoshiii\Framework\Contracts\Abstracts\AbstractTable;
use OkaniYoshiii\Framework\Contracts\Abstracts\ShellCommand;
use OkaniYoshiii\Framework\Database;
use OkaniYoshiii\Framework\Helpers\EntityBuilder;
use OkaniYoshiii\Framework\Helpers\StringHelper;
use OkaniYoshiii\Framework\Helpers\TypeConverter;
use OkaniYoshiii\Framework\ShellProgram;
use OkaniYoshiii\Framework\Types\Primitive\CamelCaseWord;
use OkaniYoshiii\Framework\Types\Primitive\FQCN;
use OkaniYoshiii\Framework\Types\Primitive\PascalCaseWord;
use OkaniYoshiii\Framework\Types\Primitive\SnakeCaseWord;

final class SynchronizeEntity extends ShellCommand
{
    public const CMD_NAME = 'entity:sync';
    public static function configureRequirements() : array
    {
        return [];
    }

    public static function execute() : void
    {
        $database = Database::getInstance();
        $database->connect();
        
        $tables = $database->getTables();

        foreach($tables as $table)
        {
            self::generateEntityFile($database, $table);
            self::generateTableFile($database, $table);
        }

        $database->disconnect();
    }

    private static function generateEntityFile(Database $database, string $table) : void
    {
        $fields = $database->getTableFields($table);

        $entityName = self::tableNameToEntityName($table);

        $entityBuilder = new EntityBuilder($entityName);

        foreach($fields as $field)
        {
            $propertyName = match(true) {
                StringHelper::isCamelCase($field) => new CamelCaseWord($field),
                StringHelper::isPascalCase($field) => StringHelper::pascalCaseToCamelCase(new PascalCaseWord($field)),
                StringHelper::isSnakeCase($field) => StringHelper::snakeCaseToCamelCase(new SnakeCaseWord($field)),
            };

            $meta = $database->getFieldMeta($table, $field);

            $type = TypeConverter::pdoTypeToPhpType($meta->getPdoType())->typeDeclaration();
            $isNullable = (array_search('not_null', $meta->getFlags(), true) === false);

            $entityBuilder
                ->addProperty($propertyName, $type)
                ->mapSQLFieldToProperty($propertyName, $field, $isNullable)
                ->addGetterMethod($propertyName, $type)
                ->addSetterMethod($propertyName, $type);
        }

        $entityBuilder->createPhpFile();
    }

    private static function generateTableFile(Database $database, string $table) : void
    {
        $entityName = self::tableNameToEntityName($table);

        $phpFile = (new PhpFile)
            ->setStrictTypes(true);
        $namespace = $phpFile
            ->addNamespace('App\\Tables')
            ->addUse(AbstractTable::class)
            ->addUse('App\\Entities\\' . $entityName)
            ->addUse(FQCN::class);
        $class = $namespace
            ->addClass($entityName . 'Table')
            ->setExtends(AbstractTable::class);
        
        $class
            ->addMethod('configureEntityFqcn')
            ->setReturnType(FQCN::class)
            ->setBody(
                <<<BODY
                    return new FQCN($entityName::class);
                BODY
            );

        $tableDir = './src/Tables';
        if(!is_dir($tableDir)) mkdir($tableDir);

        $filePath = $tableDir . '/' . $entityName . 'Table.php';
        file_put_contents($filePath, $phpFile);
    }

    private static function tableNameToEntityName(string $table) : PascalCaseWord
    {
        return match(true) {
            StringHelper::isCamelCase($table) => StringHelper::camelCaseToPascalCase(new CamelCaseWord($table)),
            StringHelper::isPascalCase($table) => new PascalCaseWord($table),
            StringHelper::isSnakeCase($table) => StringHelper::snakeCaseToPascalCase(new SnakeCaseWord($table)),
            default => throw new Exception('MySQL table : ' . $table . ' is not formmated either on camelCase, PascalCase ou snake_case')
        };
    }
}
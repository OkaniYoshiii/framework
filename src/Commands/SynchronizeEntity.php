<?php

namespace OkaniYoshiii\Framework\Commands;

use Exception;
use Nette\PhpGenerator\PhpFile;
use OkaniYoshiii\Framework\Contracts\Abstracts\ShellCommand;
use OkaniYoshiii\Framework\Database;
use OkaniYoshiii\Framework\Helpers\EntityBuilder;
use OkaniYoshiii\Framework\Helpers\StringHelper;
use OkaniYoshiii\Framework\Helpers\TypeConverter;
use OkaniYoshiii\Framework\ShellProgram;
use OkaniYoshiii\Framework\Types\Primitive\CamelCaseWord;
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
            $entityName = match(true) {
                StringHelper::isCamelCase($table) => StringHelper::camelCaseToPascalCase(new CamelCaseWord($table)),
                StringHelper::isPascalCase($table) => new PascalCaseWord($table),
                StringHelper::isSnakeCase($table) => StringHelper::snakeCaseToPascalCase(new SnakeCaseWord($table)),
                default => throw new Exception('MySQL table : ' . $table . ' is not formmated either on camelCase, PascalCase ou snake_case')
            };
    
            $entityBuilder = new EntityBuilder($entityName);
    
            $fields = $database->getTableFields($table);
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

        $database->disconnect();
    }
}
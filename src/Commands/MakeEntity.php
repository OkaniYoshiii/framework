<?php

namespace OkaniYoshiii\Framework\Commands;

use Exception;
use OkaniYoshiii\Framework\App;
use OkaniYoshiii\Framework\Contracts\Interfaces\ShellCommand;
use OkaniYoshiii\Framework\Database;
use OkaniYoshiii\Framework\Enums\SQLFieldType;
use OkaniYoshiii\Framework\Helpers\StringHelper;
use OkaniYoshiii\Framework\ShellProgram;
use OkaniYoshiii\Framework\Types\EntityDto;
use OkaniYoshiii\Framework\Types\ObjectCollection;
use OkaniYoshiii\Framework\Types\Primitive\PascalCaseWord;
use OkaniYoshiii\Framework\Types\Primitive\SnakeCaseWord;
use OkaniYoshiii\Framework\Types\Primitive\TitleCaseWord;
use OkaniYoshiii\Framework\Types\Primitive\Word;
use OkaniYoshiii\Framework\Types\SQLField;
use OkaniYoshiii\Framework\Types\SQLTable;

class MakeEntity implements ShellCommand  
{
    public const CMD_NAME = 'entity:make';

    private static Word $primaryKey;
    private static PascalCaseWord $entityName;
    private static ObjectCollection $entityProperties;
    private static Database $database;

    public static function execute() : void
    {
        self::$database = Database::getInstance();
        self::$database->connect();

        self::$entityProperties = new ObjectCollection(SQLField::class);
        self::askEntityConfiguration();

        $isValidated = self::askValidate();
        ShellProgram::addBreakLine();
        if($isValidated) {
            self::saveEntityAsJSON();
            self::createTable();
        }

        $isAddingAnotherProperty = self::askAddAnotherEntity();
        ShellProgram::addBreakLine();
        if($isAddingAnotherProperty){
            call_user_func(__METHOD__);
        }

        self::$database->disconnect();
    }

    private static function saveEntityAsJSON() : void
    {
        $properties = array_map(fn(SQLField $property) => $property->toArray(), self::$entityProperties->getItems());
        $sqlTable = new SQLTable(self::$entityName, self::$primaryKey, ...$properties);

        if(!is_dir(App::CACHE_DIR)) mkdir(App::CACHE_DIR);

        file_put_contents(App::CACHE_DIR . $sqlTable->getName() . '.json', json_encode($sqlTable->toArray()));
    }

    private static function createTable() : void
    {
        $table = StringHelper::camelCaseToSnakeCase(self::$entityName);
        $fields = array_map(fn(SQLField $property) : string => $property->getDatabaseMapping(), self::$entityProperties->getItems());

        self::$database->createTable($table, ...$fields);
    }

    private static function askEntityConfiguration() : void
    {
        self::$entityName = self::askClassName();
        ShellProgram::addBreakLine();

        $tableName = StringHelper::pascalCaseToSnakeCase(self::$entityName);
        if(self::$database->tableExists($tableName)) {
            ShellProgram::displayErrorMessage('L\'entité ' . self::$entityName . ' existe déjà. Si vous souhaitez la modifier, utilisez plutot la commande : ' . ModifyEntity::CMD_NAME);
            call_user_func(__METHOD__);
        }

        self::addPrimaryKey(self::$entityName);

        self::askProperties();
        ShellProgram::addBreakLine();
    }

    private static function askClassName() : PascalCaseWord
    {
        $entityName = ShellProgram::askOpenEndedQuestion('Quel nom de classe souhaitez vous donner à votre entité ?');

        if(!StringHelper::isPascalCase($entityName)) {
            ShellProgram::displayErrorMessage($entityName . ' n\'est pas formaté en PascalCase');
            return call_user_func(__METHOD__);
        }

        return new PascalCaseWord($entityName);
    }

    private static function askProperties() : void
    {
        $name = self::askPropertyName();

        ShellProgram::addBreakLine();

        $type = self::askPropertyType();

        ShellProgram::addBreakLine();

        $length = null;
        if($type->maxLength() !== null && $type->length() === null) {
            $length = self::askPropertyLength($type);
            ShellProgram::addBreakLine();
        } else {
            $length = $type->length();
        }

        $isNullable = ShellProgram::askBooleanQuestion('Est ce que cette propriété peut être nulle ?');

        $property = new SQLField($name, $type, $isNullable);
        if(isset($length) && $length !== null) $property->setLength($length);
        self::$entityProperties->addItem($property);

        ShellProgram::addBreakLine();
        $isAddingAnotherProperty = ShellProgram::askBooleanQuestion('Souhaitez-vous rajouter une propriété ?');
        
        if($isAddingAnotherProperty) {
            ShellProgram::addBreakLine();
            call_user_func(__METHOD__);
        }
    }

    private static function addPrimaryKey(SnakeCaseWord $name) : void
    {
        self::$primaryKey = $name . '_id';
        $property = new SQLField(self::$primaryKey, SQLFieldType::INTEGER, false);
        $property->setLength(11);
        $property->setIsPrimaryKey(true);
        $property->setIsUnsigned(true);

        self::$entityProperties->addItem($property);
    }

    private static function askPropertyName() : string
    {
        $name = ShellProgram::askOpenEndedQuestion('Ajoutez une propriété à cette classe :');

        if(!StringHelper::isCamelCase($name) && !StringHelper::isSnakeCase($name)) {
            ShellProgram::displayErrorMessage($name . ' n\'est pas formatté en camelCase ou snake_case');
            unset($name);
            return call_user_func(__METHOD__);
        }

        return $name;
    }

    private static function askPropertyType() : SQLFieldType
    {
        $type = ShellProgram::askCloseEndedQuestion('De quel type est cette propriété ?', SQLFieldType::values());

        return SQLFieldType::from($type);
    }

    private static function askPropertyLength(SQLFieldType $type) : int
    {
        if($type->maxLength() === null) throw new Exception(__METHOD__ . ' can only be used when $type has a maxLength.');

        $length = ShellProgram::askOpenEndedQuestion('Quelle longueur maximale peut avoir cette propriété dans la base de données ? (longueur maximale : ' . $type->maxLength() . ')', asInteger : true);

        if($length > $type->maxLength()) {
            ShellProgram::displayErrorMessage($length . ' est supérieur à la longueur maximale de ce type de propriété (' . $type->maxLength() . ')');
            return call_user_func(__METHOD__, $type);
        }

        return $length;
    }

    private static function askValidate() : bool
    {
        return ShellProgram::askBooleanQuestion('Voici l\'entité nouvellement configurée : ' . PHP_EOL . PHP_EOL . self::buildEntityRepresentation() . PHP_EOL . 'Êtes vous sur de vos choix ?');
    }

    private static function buildEntityRepresentation() : string
    {
        $entityName = self::$entityName;
        $entityProperties = implode(PHP_EOL . "\t- ", self::$entityProperties->getItems());

        return <<<ENTITY
        Entité : $entityName;
        Propriétés :
        \t- $entityProperties
        ENTITY;
    }

    private static function askAddAnotherEntity() : bool
    {
        return ShellProgram::askBooleanQuestion('Voulez vous créer une nouvelle entité ?');
    }
}
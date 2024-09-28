<?php

namespace Framework\Commands;

use Exception;
use Framework\Contracts\Interfaces\ShellCommand;
use Framework\Database;
use Framework\Enums\TablePropertyType;
use Framework\Helpers\StringHelper;
use Framework\ShellProgram;
use Framework\Types\ObjectCollection;
use Framework\Types\TableProperty;

class MakeEntity implements ShellCommand  
{
    public const CMD_NAME = 'entity:make';

    private static string $entityName;
    private static ObjectCollection $entityProperties;
    private static Database $database;

    public static function execute() : void
    {
        self::$database = Database::getInstance();
        self::$database->connect();

        self::$entityProperties = new ObjectCollection(TableProperty::class);
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
        $entity = ['name' => self::$entityName, 'table' => StringHelper::camelCaseToSnakeCase(self::$entityName)];
        $entity['properties'] = array_map(function(TableProperty $property) {
            return [
                'name' => $property->getName(),
                'field' => TableProperty::getMappedName($property->getName()),
                'type' => $property->getType()->value,
                'isNullable' => $property->getIsNullable(),
            ];
        }, self::$entityProperties->getItems());

        $cacheDir = './framework/cache/';
        if(is_dir($cacheDir)) {
            file_put_contents('./framework/cache/' . $entity['name'] . '.json', json_encode($entity));
        } else {
            mkdir('./framework/cache/');
            file_put_contents('./framework/cache/' . $entity['name'] . '.json', json_encode($entity));
        }
    }

    private static function createTable() : void
    {
        $table = StringHelper::camelCaseToSnakeCase(self::$entityName);
        $fields = array_map(fn(TableProperty $property) : string => $property->getDatabaseMapping(), self::$entityProperties->getItems());

        self::$database->createTable($table, ...$fields);
    }

    private static function askEntityConfiguration() : void
    {
        self::$entityName = self::askClassName();
        ShellProgram::addBreakLine();

        if(self::$database->tableExists(TableProperty::getMappedName(self::$entityName))) {
            ShellProgram::displayErrorMessage('L\'entité ' . self::$entityName . ' existe déjà. Si vous souhaitez la modifier, utilisez plutot la commande : ' . ModifyEntity::CMD_NAME);
            call_user_func(__METHOD__);
        }

        self::addPrimaryKey(self::$entityName);

        self::askProperties();
        ShellProgram::addBreakLine();
    }

    private static function askClassName() : string
    {
        $entityName = ShellProgram::askOpenEndedQuestion('Quel nom de classe souhaitez vous donner à votre entité ?');

        if(!StringHelper::isTitleCase($entityName)) {
            ShellProgram::displayErrorMessage($entityName . ' n\'est pas formatté en TitleCase');
            return call_user_func(__METHOD__);
        }

        return $entityName;
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

        $property = new TableProperty($name, $type, $isNullable);
        if(isset($length) && $length !== null) $property->setLength($length);
        self::$entityProperties->addItem($property);

        ShellProgram::addBreakLine();
        $isAddingAnotherProperty = ShellProgram::askBooleanQuestion('Souhaitez-vous rajouter une propriété ?');
        
        if($isAddingAnotherProperty) {
            ShellProgram::addBreakLine();
            call_user_func(__METHOD__);
        }
    }

    private static function addPrimaryKey(string $name) : void
    {
        $name = StringHelper::camelCaseToSnakeCase($name) . '_id';
        $property = new TableProperty($name, TablePropertyType::INTEGER, false);
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

    private static function askPropertyType() : TablePropertyType
    {
        $type = ShellProgram::askCloseEndedQuestion('De quel type est cette propriété ?', TablePropertyType::values());

        return TablePropertyType::from($type);
    }

    private static function askPropertyLength(TablePropertyType $type) : int
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
<?php

namespace Framework\Commands;

use Exception;
use Framework\Contracts\Interfaces\ShellCommand;
use Framework\Database;
use Framework\Enums\DataType;
use Framework\Enums\StringCase;
use Framework\Enums\TablePropertyType;
use Framework\Exceptions\IncorrectStringCaseException;
use Framework\Helpers\StringHelper;
use Framework\ShellProgram;
use Framework\Types\ObjectCollection;
use Framework\Types\TableProperty;

class MakeEntity implements ShellCommand
{
    private static string $entityName;
    private static ObjectCollection $entityProperties;

    public static function execute(array $options) : void
    {
        self::$entityProperties = new ObjectCollection(TableProperty::class);
        self::createTable();
    }

    private static function createTable() : void
    {
        self::$entityName = self::askClassName();
        ShellProgram::addBreakLine();

        self::askProperties();
        ShellProgram::addBreakLine();

        $isValidated = self::askValidate();

        if($isValidated) {
            $database = Database::getInstance();
            $database->connect();

            // $table = StringHelper::camelCaseToSnakeCase(self::$entityName);
            // $fields = 
            // $sqlQuery = 'CREATE TABLE IF NOT EXISTS ' . $table . '(' . implode(', ',$fields) . ')';
            // var_dump($sqlQuery);
            // $database->getPdo()->query($sqlQuery);
        }
    }

    private static function askClassName() : string
    {
        $entityName = ShellProgram::askOpenEndedQuestion('Quel nom de classe souhaitez vous donner à votre entité ?');

        if(!StringHelper::isTitleCase($entityName)) {
            echo (new IncorrectStringCaseException($entityName, [StringCase::TITLE_CASE]))->getMessage();
            echo PHP_EOL;
            call_user_func(__METHOD__);
        }

        return $entityName;
    }

    private static function askProperties() : void
    {
        $property['name'] = self::askPropertyName();

        ShellProgram::addBreakLine();

        $property['type'] = self::askPropertyType();

        ShellProgram::addBreakLine();

        $property['isNullable'] = ShellProgram::askBooleanQuestion('Est ce que cette propriété peut être nulle ?');

        $property = new TableProperty(...$property);
        self::$entityProperties->addItem($property);

        $isAddingAnotherProperty = ShellProgram::askBooleanQuestion('Souhaitez-vous rajouter une propriété ?');
        ShellProgram::addBreakLine();
        
        if($isAddingAnotherProperty) {
            call_user_func(__METHOD__);
        }
    }

    private static function askPropertyName() : string
    {
        $name = ShellProgram::askOpenEndedQuestion('Ajoutez une propriété à cette classe :');

        if(!StringHelper::isCamelCase($name) && !StringHelper::isSnakeCase($name)) {
            echo (new IncorrectStringCaseException($name, [StringCase::SNAKE_CASE, StringCase::CAMEL_CASE]))->getMessage();
            echo PHP_EOL;
            call_user_func(__METHOD__);
        }

        return $name;
    }

    private static function askPropertyType() : TablePropertyType
    {
        $type = ShellProgram::askCloseEndedQuestion('De quel type est cette propriété ?', TablePropertyType::values());

        return TablePropertyType::from($type);
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
}
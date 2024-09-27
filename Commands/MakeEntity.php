<?php

namespace Framework\Commands;

use Exception;
use Framework\Contracts\Interfaces\ShellCommand;
use Framework\Enums\StringCase;
use Framework\Exceptions\IncorrectStringCaseException;
use Framework\Helpers\StringHelper;
use Framework\ShellProgram;

class MakeEntity implements ShellCommand
{
    private static string $entityName;
    private static array $entityProperties = [];

    public static function execute(array $options) : void
    {
        self::createTable();
    }

    private static function createTable() : void
    {
        self::$entityName = ShellProgram::askQuestion('Quel nom de classe souhaitez vous donner à votre entité ?' . PHP_EOL);
        ShellProgram::addBreakLine();

        // var_dump(StringHelper::isCamelCase(self::$entityName), StringHelper::isSnakeCase(self::$entityName));

        if(!StringHelper::isTitleCase(self::$entityName)) {
            throw new IncorrectStringCaseException(self::$entityName, [StringCase::TITLE_CASE]);
        }

        self::askAddingProperties();
        ShellProgram::addBreakLine();

        $entityRepresentation = self::buildEntityRepresentation();
        $isValidated = ShellProgram::askQuestion('Voici l\'entité nouvellement configurée : ' . PHP_EOL . $entityRepresentation . PHP_EOL . 'Êtes vous sur de vos choix ? (Oui/Non)', ['Oui', 'Non']);


        // $database = Database::getInstance();
        // $database->connect();

        // $database->getPdo()->query('CREATE TABLE IF NOT EXISTS ' . $tableName . ' (  )');
    }

    private static function askAddingProperties() : void
    {
        $property = ShellProgram::askQuestion('Ajoutez une propriété à cette classe :' . PHP_EOL);
        self::$entityProperties[] = $property;
        ShellProgram::addBreakLine();

        if(!StringHelper::isCamelCase($property) && !StringHelper::isSnakeCase($property)) {
            throw new IncorrectStringCaseException($property, [StringCase::SNAKE_CASE, StringCase::CAMEL_CASE]);
        }

        $isAddingAnotherProperty = ShellProgram::askQuestion('Souhaitez-vous rajouter une propriété ? (Oui/Non)' . PHP_EOL, ['Oui', 'Non']);
        
        if($isAddingAnotherProperty === 'Oui') {
            ShellProgram::addBreakLine();
            self::askAddingProperties();
        }
    }

    private static function buildEntityRepresentation() : string
    {
        $entityName = self::$entityName;
        $entityProperties = implode(', ', self::$entityProperties);
        return <<<ENTITY
        Entité : $entityName;
        Propriétés : $entityProperties
        ENTITY;
    }
}
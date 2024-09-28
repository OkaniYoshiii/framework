<?php

namespace Framework\Commands;

use Framework\Contracts\Interfaces\ShellCommand;
use Framework\Database;
use Framework\Enums\TableRelation;
use Framework\Helpers\StringHelper;
use Framework\ShellProgram;

class LinkEntity implements ShellCommand
{
    public const CMD_NAME = 'entity:link';

    private static Database $database;
    private static string $entityName;
    private static string $linkedEntityName;
    private static TableRelation $relation;

    public static function execute() : void
    {
        self::$database = Database::getInstance();
        self::$database->connect();

        self::$entityName = self::askEntityName();
        ShellProgram::addBreakLine();

        self::$linkedEntityName = self::askLinkedTo();
        ShellProgram::addBreakLine();

        self::$relation = self::askRelationType();
        ShellProgram::addBreakLine();

        self::persistRelation();

        self::$database->disconnect();
    }

    private static function askEntityName() : string
    {
        $tables = self::$database->getTables();
        $tables = array_map(fn(string $table) => StringHelper::toTitleCase($table), $tables);

        return ShellProgram::askCloseEndedQuestion('Quelle entité souhaitez vous lier ?', $tables);
    }

    private static function persistRelation() : void
    {

    }

    private static function askLinkedTo() : string
    {
        $tables = self::$database->getTables();
        $entityTable = StringHelper::camelCaseToSnakeCase(self::$entityName);
        unset($tables[array_search($entityTable, $tables)]);

        if(empty($tables)) {
            ShellProgram::displayErrorMessage('Impossible de réaliser une liaison : Aucune autre entité existe. Utilisez : ' . MakeEntity::CMD_NAME . ' pour créer une nouvelle entité.');
            ShellProgram::close();
        }

        $tables = array_map(fn(string $table) => StringHelper::toTitleCase($table), $tables);

        return ShellProgram::askCloseEndedQuestion('A quelle entité la liaison se fera ?', $tables);
    }

    private static function askRelationType() : TableRelation
    {
        $tables = self::$database->getTables();
        $tables = array_map(fn(string $table) => StringHelper::toTitleCase($table), $tables);

        $oneToMany = TableRelation::ONE_TO_MANY->value;
        $manyToOne = TableRelation::MANY_TO_ONE->value;
        $manyToMany = TableRelation::MANY_TO_MANY->value;
        $entityName = self::$entityName;
        $linkedEntityName = self::$linkedEntityName;

        $helpMessage = <<<HELP
        {$oneToMany} : Un(e) {$entityName} est lié(e) à un(e) ou plusieurs {$linkedEntityName}. Un(e) ou plusieurs {$linkedEntityName} sont lié(e)s à un(e) {$entityName}.
        {$manyToOne} : Un(e) ou plusieurs {$entityName} sont lié(e)s à un(e) {$linkedEntityName}. Un(e) {$linkedEntityName} est lié(e) à un(e) ou plusieurs {$entityName}.
        {$manyToMany} : Un(e) ou plusieurs {$entityName} sont lié(e)s à un(e) ou plusieurs {$linkedEntityName}.
        HELP;

        $relation = ShellProgram::askCloseEndedQuestion('Quelle relation y a t-il entre ces deux entités ?', TableRelation::values(), $helpMessage);

        return TableRelation::from($relation);
    }
}
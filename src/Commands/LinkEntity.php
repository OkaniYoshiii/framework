<?php

namespace OkaniYoshiii\Framework\Commands;

use OkaniYoshiii\Framework\App;
use OkaniYoshiii\Framework\Contracts\Interfaces\ShellCommand;
use OkaniYoshiii\Framework\Database;
use OkaniYoshiii\Framework\Enums\TableRelation;
use OkaniYoshiii\Framework\Helpers\StringHelper;
use OkaniYoshiii\Framework\ShellProgram;
use OkaniYoshiii\Framework\Types\EntityDto;

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
        $entityDataPath = './framework/cache/' . self::$entityName . '.json';
        $linkedEntityDataPath = './framework/cache/' . self::$linkedEntityName . '.json';

        if(!file_exists($entityDataPath)  || !file_exists($linkedEntityDataPath)) {
            ShellProgram::displayErrorMessage('Afin de pouvoir persister la relation en base de données, des fichiers de configuration doivent être générés par la commande : ' . MakeEntity::CMD_NAME . '. Or, ces fichiers n\'existent pas');
            ShellProgram::close();
        }

        $entity = json_decode(file_get_contents($entityDataPath), true);
        $linkedEntity = json_decode(file_get_contents($linkedEntityDataPath), true);

        $entity = new EntityDto($entity['name'], $entity['primaryKey'], ...$entity['properties']);
        $linkedEntity = new EntityDto($linkedEntity['name'], $linkedEntity['primaryKey'], ...$linkedEntity['properties']);

        self::$database->addForeignkey($entity, $linkedEntity);
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
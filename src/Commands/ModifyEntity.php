<?php

namespace OkaniYoshiii\Framework\Commands;

use OkaniYoshiii\Framework\Contracts\Abstracts\ShellCommand;
use OkaniYoshiii\Framework\Database;
use OkaniYoshiii\Framework\Helpers\StringHelper;
use OkaniYoshiii\Framework\ShellProgram;
use OkaniYoshiii\Framework\Types\Test;

class ModifyEntity extends ShellCommand
{
    const CMD_NAME = 'entity:modify';

    private static Database $database;
    private static string $entityName;

    protected static function configureRequirements(): array
    {
        $message = 'Cannot connect to database';
        $test = function() {
            $database = Database::getInstance();
            $database->connect();
        };
        
        $canConnectToDatabase = new Test($message, $test);

        return [
            $canConnectToDatabase,
        ];
    }

    public static function execute() : void
    {
        self::$database = Database::getInstance();
        self::$database->connect();

        self::$entityName = self::askEntityName();
        ShellProgram::addBreakLine();

        ShellProgram::addBreakLine();

        self::$database->disconnect();
    }

    public static function askEntityName() : string
    {
        $tables = self::$database->getTables();
        $tables = array_map(fn(string $table) => StringHelper::toPascalCase($table), $tables);

        return ShellProgram::askCloseEndedQuestion('Quelle entité souhaitez vous modifier ?', $tables);
    }
}
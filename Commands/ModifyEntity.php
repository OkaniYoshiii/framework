<?php

namespace Framework\Commands;

use Framework\Contracts\Interfaces\ShellCommand;
use Framework\Database;
use Framework\Helpers\StringHelper;
use Framework\ShellProgram;

class ModifyEntity implements ShellCommand
{
    const CMD_NAME = 'entity:modify';

    private static Database $database;
    private static string $entityName;

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
        $tables = array_map(fn(string $table) => StringHelper::toTitleCase($table), $tables);

        return ShellProgram::askCloseEndedQuestion('Quelle entité souhaitez vous modifier ?', $tables);
    }
}
<?php

namespace OkaniYoshiii\Framework\Commands;

use OkaniYoshiii\Framework\Contracts\Interfaces\ShellCommand;
use OkaniYoshiii\Framework\Database;
use OkaniYoshiii\Framework\Helpers\StringHelper;
use OkaniYoshiii\Framework\ShellProgram;

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
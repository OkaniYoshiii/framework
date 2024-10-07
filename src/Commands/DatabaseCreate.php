<?php

namespace OkaniYoshiii\Framework\Commands;

use OkaniYoshiii\Framework\Contracts\Interfaces\ShellCommand;
use OkaniYoshiii\Framework\Database;

class DatabaseCreate implements ShellCommand
{
    public const CMD_NAME = 'database:create';
    
    public static function execute() : void
    {
        $database = Database::getInstance();
        $database->connectAsAdmin();
        $database->create();
        $database->disconnect();
    }
}
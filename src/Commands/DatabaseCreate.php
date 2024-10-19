<?php

namespace OkaniYoshiii\Framework\Commands;

use Exception;
use OkaniYoshiii\Framework\Contracts\Abstracts\ShellCommand;
use OkaniYoshiii\Framework\Database;

class DatabaseCreate extends ShellCommand
{
    public const CMD_NAME = 'database:create';

    protected static function configureRequirements() : array
    {
        return [];
    }
    
    protected static function execute() : void
    {        
        $database = Database::getInstance();
        $database->connectAsAdmin();
        $database->create();
        $database->disconnect();
    }
}
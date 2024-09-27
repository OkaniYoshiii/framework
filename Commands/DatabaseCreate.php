<?php

namespace Framework\Commands;

use Framework\Contracts\Interfaces\ShellCommand;
use Framework\Database;

class DatabaseCreate implements ShellCommand
{
    public static function execute(array $options) : void
    {
        $database = Database::getInstance();
        $database->connectAsAdmin();
        $database->create();
        $database->disconnect();
    }
}
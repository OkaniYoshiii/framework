<?php

namespace OkaniYoshiii\Framework\Contracts\Abstracts;

use Exception;
use OkaniYoshiii\Framework\ShellProgram;
use OkaniYoshiii\Framework\Types\Requirement;
use OkaniYoshiii\Framework\Types\Test;

abstract class ShellCommand
{
    public static function setupAndExecute() : void
    {
        $tests = static::configureRequirements();

        foreach($tests as $test) 
        {
            if(!($test instanceof Test)) {
                throw new Exception('');
            }

            if($test->isValidated() === false) {
                throw new Exception('Requirement failed : "' . $test->getMessage() . '".' . ' Exception : ' . $test->getException()->getMessage());
            }
        }

        static::execute();
    }

    abstract protected static function execute() : void;

    abstract protected static function configureRequirements() : array;
}
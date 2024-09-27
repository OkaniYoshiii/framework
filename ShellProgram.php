<?php

namespace Framework;

use Dotenv\Dotenv;
use Framework\Commands\DatabaseCreate;
use Framework\Commands\MakeEntity;
use Framework\Shell\Commands\Init;

class ShellProgram
{
    public static function start(array $argv)
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../', '.env.local');
        $dotenv->load();

        match($argv[1]) {
            'database:create' => DatabaseCreate::execute([]),
            'init' => Init::execute([]),
            'make:entity' => MakeEntity::execute([]),
            default => null,
        };
    }

    public static function askQuestion($question, $answers = null)
    {
        echo $question.' ';
        $handle = fopen("php://stdin","r");
        $answer = fgets($handle);
        fclose($handle);
        $answer = trim($answer);
        if($answers != null) {
            if( !in_array($answer,$answers) ) {
                return self::askQuestion($question, $answers);
            }
        }
        return $answer;
    }
    
    public static function addBreakLine() : void
    {
        echo PHP_EOL . '---------------------------' . PHP_EOL;
    }
}
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

    /**
     * Pose une question OUVERTE dans le Shell
     * 
     * @param string $question
     * @return string
     */
    public static function askOpenEndedQuestion(string $question) : string
    {
        echo $question;
        echo PHP_EOL;
        $handle = fopen("php://stdin","r");
        $answer = fgets($handle);
        fclose($handle);
        $answer = trim($answer);

        return $answer;
    }

    /**
     * Pose une question FERMEE dans le Shell
     * 
     * Permet de poser une question dont la réponse est prédéfinies par rapport à
     * une selection. Afin de définir les réponses possibles, le paramètres $answers
     * doit être un array dont les valeurs correspondent aux réponses possibles.
     * 
     * @param string $question
     * @param array $answers
     */
    public static function askCloseEndedQuestion(string $question, array $answers) : string
    {
        $question = $question . ' (' . implode('/', $answers) . ')';
        $answer = self::askOpenEndedQuestion($question);

        if(!in_array($answer,$answers) ) {
            return call_user_func(__METHOD__, $question, $answers);
        }

        return $answer;
    }

    /**
     * Pose une question FERMEE dont la réponse est un booléen (VRA/FAUX)
     * 
     * Exemple : Aimes tu les pâtes ?
     * 
     * @param string $question La question à poser
     * @return bool
     */
    public static function askBooleanQuestion(string $question) : bool
    {
        $answersMapping = ['O' => true, 'N' => false];

        $answer = ShellProgram::askCloseEndedQuestion($question, array_keys($answersMapping));

        return $answersMapping[$answer];
    }
    
    public static function addBreakLine() : void
    {
        echo PHP_EOL . '---------------------------' . PHP_EOL;
    }
}
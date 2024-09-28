<?php

namespace Framework;

use Dotenv\Dotenv;
use Framework\Commands\DatabaseCreate;
use Framework\Commands\Init;
use Framework\Commands\MakeEntity;
use Framework\Commands\ModifyEntity;
use Framework\Enums\DataType;

class ShellProgram
{
    public static function start(array $argv)
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../', '.env.local');
        $dotenv->load();

        match($argv[1]) {
            DatabaseCreate::CMD_NAME => DatabaseCreate::execute(),
            Init::CMD_NAME => Init::execute(),
            MakeEntity::CMD_NAME => MakeEntity::execute(),
            ModifyEntity::CMD_NAME => ModifyEntity::execute(),
            default => null,
        };
    }

    public static function displayErrorMessage(string $message) : void
    {
        echo PHP_EOL;
        echo 'ERREUR : ' . $message;
        echo PHP_EOL;
        self::addBreakLine();
    }

    public static function waitForAnswer() : string
    {
        $handle = fopen("php://stdin","r");
        $answer = fgets($handle);
        fclose($handle);

        if($answer === false) die('Fermeture du programme');
        $answer = trim($answer);

        return trim($answer);
    }

    /**
     * Pose une question OUVERTE dans le Shell
     * 
     * @param string $question
     * @return string
     */
    public static function askOpenEndedQuestion(string $question, bool $asInteger = false) : string|int
    {
        echo $question;
        echo PHP_EOL;

        $answer = self::waitForAnswer();

        if(empty($answer)) {
            self::displayErrorMessage('La question est obligatoire');
            return call_user_func($question, $asInteger);
        }

        if($asInteger) {
            if(!is_numeric($answer)) {
                self::displayErrorMessage('La valeur doit être de type : ' . DataType::INTEGER->value);
                return call_user_func(__METHOD__, $question, $asInteger);
            }

            $answer = intval($answer);
        } 

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
        $formattedQuestion = $question . ' (' . implode('/', $answers) . ')';
        $answer = self::askOpenEndedQuestion($formattedQuestion);

        if(!in_array($answer,$answers) ) {
            self::displayErrorMessage($answer . ' ne fait pas partie des réponses proposées');
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
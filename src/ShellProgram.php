<?php

namespace OkaniYoshiii\Framework;

use Dotenv\Dotenv;
use Exception;
use OkaniYoshiii\Framework\Commands\DatabaseCreate;
use OkaniYoshiii\Framework\Commands\Init;
use OkaniYoshiii\Framework\Commands\LinkEntity;
use OkaniYoshiii\Framework\Commands\MakeEntity;
use OkaniYoshiii\Framework\Commands\ModifyEntity;
use OkaniYoshiii\Framework\Enums\DataType;
use Throwable;

class ShellProgram
{
    public static function start(array $argv)
    {
        match($argv[1]) {
            // DatabaseCreate::CMD_NAME => DatabaseCreate::setupAndExecute(),
            // Init::CMD_NAME => Init::setupAndExecute(),
            MakeEntity::CMD_NAME => MakeEntity::setupAndExecute(),
            // ModifyEntity::CMD_NAME => ModifyEntity::setup(),
            // LinkEntity::CMD_NAME => LinkEntity::setup(),
            default => self::displayErrorMessage('La commande spécifiée n\'existe pas'),
        };
    }

    public static function close() 
    {
        self::addBreakLine();
        echo 'Fermeture du programme';
        self::addBreakLine();
        die();
    }

    public static function displayErrorMessage(string $message) : void
    {
        echo PHP_EOL;
        echo 'ERREUR : ' . $message;
        echo PHP_EOL;
        self::addBreakLine();
    }

    public static function displayHelpMessage(string $message) : void
    {
        echo PHP_EOL;
        echo '### AIDE ###' . PHP_EOL . $message;
        echo PHP_EOL;
        self::addBreakLine();
    }

    public static function waitForAnswer() : string
    {
        $handle = fopen("php://stdin","r");
        $answer = fgets($handle);
        fclose($handle);

        if($answer === false) self::close();
        $answer = trim($answer);

        return trim($answer);
    }

    /**
     * Pose une question OUVERTE dans le Shell
     * 
     * @param string $question
     * @return string
     */
    public static function askOpenEndedQuestion(string $question, bool $asInteger = false, string|int|null $defaultAnswer = null) : string|int
    {
        echo $question;
        echo PHP_EOL;

        $answer = self::waitForAnswer();

        if(empty($answer) && $defaultAnswer === null) {
            self::displayErrorMessage('La question est obligatoire');
            return call_user_func(__METHOD__, $question, $asInteger);
        }

        if($asInteger) {
            if(!is_numeric($answer)) {
                self::displayErrorMessage('La valeur doit être de type : ' . DataType::INTEGER->value);
                return call_user_func(__METHOD__, $question, $asInteger);
            }

            $answer = intval($answer);
        }

        return (empty($answer)) ? $defaultAnswer : $answer;
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
    public static function askCloseEndedQuestion(string $question, array $answers, ?string $helpMessage = null, string|int|null $defaultAnswer = null) : string
    {
        $helpOption = 'help';
        $formattedQuestion = $question . ' (' . implode('/', $answers) . ')';
        $formattedQuestion .= ($helpMessage !== null) ? PHP_EOL . 'Tapez "' . $helpOption . '" pour obtenir de l\'aide supplémentaire' : '';

        if($defaultAnswer !== null && !in_array($defaultAnswer, $answers)) throw new Exception('Cannot set a default answer that is not in the possible answers list');

        $answer = self::askOpenEndedQuestion(question : $formattedQuestion, defaultAnswer : $defaultAnswer); 

        if($answer === $helpOption) {
            self::displayHelpMessage($helpMessage);
            return call_user_func(__METHOD__, $question, $answers, $helpMessage);
        }

        if(!in_array($answer,$answers) ) {
            self::displayErrorMessage('"' . $answer . '" ne fait pas partie des réponses proposées');
            return call_user_func(__METHOD__, $question, $answers, $helpMessage);
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
    public static function askBooleanQuestion(string $question, ?bool $defaultAnswer = null) : bool
    {
        $answersMapping = ['O' => true, 'N' => false];

        $defaultAnswer = ($defaultAnswer !== null) ? array_search($defaultAnswer, $answersMapping, true) : null;

        $answer = ShellProgram::askCloseEndedQuestion($question, array_keys($answersMapping), defaultAnswer : $defaultAnswer);

        return $answersMapping[$answer];
    }
    
    public static function addBreakLine() : void
    {
        echo PHP_EOL . '---------------------------' . PHP_EOL;
    }
}
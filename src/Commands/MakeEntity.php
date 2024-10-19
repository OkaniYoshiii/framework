<?php

namespace OkaniYoshiii\Framework\Commands;

use Exception;
use OkaniYoshiii\Framework\App;
use OkaniYoshiii\Framework\Contracts\Abstracts\ShellCommand;
use OkaniYoshiii\Framework\Database;
use OkaniYoshiii\Framework\Enums\SQLFieldType;
use OkaniYoshiii\Framework\Helpers\StringHelper;
use OkaniYoshiii\Framework\ShellProgram;
use OkaniYoshiii\Framework\Types\Entity;
use OkaniYoshiii\Framework\Types\EntityDto;
use OkaniYoshiii\Framework\Types\ObjectCollection;
use OkaniYoshiii\Framework\Types\Primitive\CamelCaseWord;
use OkaniYoshiii\Framework\Types\Primitive\PascalCaseWord;
use OkaniYoshiii\Framework\Types\Primitive\SnakeCaseWord;
use OkaniYoshiii\Framework\Types\Primitive\Word;
use OkaniYoshiii\Framework\Types\SQLField;
use OkaniYoshiii\Framework\Types\SQLTable;
use OkaniYoshiii\Framework\Types\Test;

class MakeEntity extends ShellCommand
{
    public const CMD_NAME = 'entity:make';

    private static Word $primaryKey;

    protected static function configureRequirements(): array
    {
        $message = 'Cannot connect to database';
        $test = function() {
            $database = Database::getInstance();
            $database->connect();
        };

        $canConnectToDatabase = new Test($message, $test);

        return [
           $canConnectToDatabase,
        ];
    }

    protected static function execute() : void
    {
        $database = Database::getInstance();
        $database->connect();

        // $entityProperties = new ObjectCollection(SQLField::class);
        $entity = self::askEntityConfiguration();

        $isValidated = self::askValidationForCreatedEntity($entity);

        ShellProgram::addBreakLine();
        if($isValidated) {
            $name = StringHelper::pascalCaseToSnakeCase($entity->getName());
            $primaryKey = new SnakeCaseWord($name->getValue() . '_id');
            $properties = $entity->getProperties();

            $table = new SQLTable($name, $primaryKey, ...$properties);
            // self::saveEntityAsJSON();
            $database->createTable($table);
        }

        $isAddingAnotherEntity = self::askAddAnotherEntity();

        ShellProgram::addBreakLine();
        
        if($isAddingAnotherEntity){
            call_user_func(__METHOD__);
        }

        $database->disconnect();
    }

    private static function askEntityConfiguration() : Entity
    {
        $name = self::askEntityName();
        ShellProgram::addBreakLine();

        // $tableName = StringHelper::pascalCaseToSnakeCase($entityName);
        // if($database->tableExists($tableName)) {
        //     ShellProgram::displayErrorMessage('L\'entité ' . $entityName . ' existe déjà. Si vous souhaitez la modifier, utilisez plutot la commande : ' . ModifyEntity::CMD_NAME);
        //     call_user_func(__METHOD__);
        // }

        // $primaryKey = self::addPrimaryKey($entityName);

        $properties = [];
        do {
            $property = self::askProperty();

            ShellProgram::addBreakLine();

            $isAddingAnotherProperty = ShellProgram::askBooleanQuestion('Souhaitez-vous rajouter une propriété ?');
            
            if($isAddingAnotherProperty === true) ShellProgram::addBreakLine();

            $properties[] = $property;
        } while($isAddingAnotherProperty === true);

        return new Entity($name, ...$properties);
    }

    private static function askEntityName() : PascalCaseWord
    {
        $entityName = ShellProgram::askOpenEndedQuestion('Quel nom de classe souhaitez vous donner à votre entité ?');

        if(!StringHelper::isWord($entityName)) {
            ShellProgram::displayErrorMessage($entityName . ' doit être un mot composé uniquement de lettres');
            return call_user_func(__METHOD__);
        }

        if(!StringHelper::isPascalCase($entityName)) {
            ShellProgram::displayErrorMessage($entityName . ' n\'est pas formaté en PascalCase');
            return call_user_func(__METHOD__);
        }

        return new PascalCaseWord($entityName);
    }

    private static function askProperty() : SQLField
    {
        $name = self::askPropertyName();

        ShellProgram::addBreakLine();

        $type = self::askPropertyType();

        ShellProgram::addBreakLine();

        $length = null;
        if($type->maxLength() !== null && $type->length() === null) {
            $length = self::askPropertyLength($type);
            ShellProgram::addBreakLine();
        } else {
            $length = $type->length();
        }

        $isNullable = ShellProgram::askBooleanQuestion('Est ce que cette propriété peut être nulle ?');

        $property = new SQLField($name, $type, $isNullable);
        if(isset($length) && $length !== null) $property->setLength($length);

        return $property;
    }

    private static function askValidationForCreatedEntity(Entity $entity) : bool
    {
        $entityStringRepresentation = self::buildEntityRepresentation($entity);
        
        return ShellProgram::askBooleanQuestion('Voici l\'entité nouvellement configurée : ' . PHP_EOL . PHP_EOL . $entityStringRepresentation . PHP_EOL . 'Êtes vous sur de vos choix ?');
    }

    private static function askPropertyName() : CamelCaseWord
    {
        $name = ShellProgram::askOpenEndedQuestion('Ajoutez une propriété à cette classe :');

        if(!StringHelper::isWord($name)) {
            ShellProgram::displayErrorMessage($name . ' doit être un mot simple composé uniquement de lettres, sans espaces ni caractères spéciaux');
            unset($name);
            return call_user_func(__METHOD__);
        }

        if(!StringHelper::isCamelCase($name)) {
            ShellProgram::displayErrorMessage($name . ' doit être formatté en camelCase');
            unset($name);
            return call_user_func(__METHOD__);
        }

        return new CamelCaseWord($name);
    }

    private static function askPropertyType() : SQLFieldType
    {
        $type = ShellProgram::askCloseEndedQuestion('De quel type est cette propriété ?', SQLFieldType::values());

        return SQLFieldType::from($type);
    }

    private static function askPropertyLength(SQLFieldType $type) : int
    {
        if($type->maxLength() === null) throw new Exception(__METHOD__ . ' can only be used when $type has a maxLength.');

        $length = ShellProgram::askOpenEndedQuestion('Quelle longueur maximale peut avoir cette propriété dans la base de données ? (longueur maximale : ' . $type->maxLength() . ')', asInteger : true);

        if($length > $type->maxLength()) {
            ShellProgram::displayErrorMessage($length . ' est supérieur à la longueur maximale de ce type de propriété (' . $type->maxLength() . ')');
            return call_user_func(__METHOD__, $type);
        }

        return $length;
    }

    // private static function saveEntityAsJSON() : void
    // {
    //     $properties = array_map(fn(SQLField $property) => $property->toArray(), $entityProperties->getItems());
    //     $sqlTable = new SQLTable($entityName, self::$primaryKey, ...$properties);

    //     if(!is_dir(App::CACHE_DIR)) mkdir(App::CACHE_DIR);

    //     file_put_contents(App::CACHE_DIR . $sqlTable->getName() . '.json', json_encode($sqlTable->toArray()));
    // }

    private static function buildEntityRepresentation(Entity $entity) : string
    {
        $properties = implode(PHP_EOL . "\t- ", $entity->getProperties());

        return <<<ENTITY
        Entité : {$entity->getName()};
        Propriétés :
        \t- $properties
        ENTITY;
    }

    private static function askAddAnotherEntity() : bool
    {
        return ShellProgram::askBooleanQuestion('Voulez vous créer une nouvelle entité ?');
    }
}
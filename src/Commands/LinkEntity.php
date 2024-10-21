<?php

namespace OkaniYoshiii\Framework\Commands;

use Exception;
use Nette\PhpGenerator\ClassManipulator;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use OkaniYoshiii\Framework\App;
use OkaniYoshiii\Framework\Contracts\Abstracts\ShellCommand;
use OkaniYoshiii\Framework\Database;
use OkaniYoshiii\Framework\Enums\DataType;
use OkaniYoshiii\Framework\Enums\SQLTableRelation;
use OkaniYoshiii\Framework\Helpers\StringHelper;
use OkaniYoshiii\Framework\ShellProgram;
use OkaniYoshiii\Framework\Types\EntityDto;
use OkaniYoshiii\Framework\Types\Primitive\CamelCaseWord;
use OkaniYoshiii\Framework\Types\Primitive\FQCN;
use OkaniYoshiii\Framework\Types\Primitive\PascalCaseWord;
use OkaniYoshiii\Framework\Types\Primitive\SnakeCaseWord;
use OkaniYoshiii\Framework\Types\SQLTable;
use OkaniYoshiii\Framework\Types\Test;

class LinkEntity extends ShellCommand
{
    public const CMD_NAME = 'entity:link';

    private static string $entityName;
    private static string $linkedEntityName;
    private static SQLTableRelation $relation;
    private static Database $database;
    private static array $entities;

    protected static function configureRequirements() : array
    {
        $canConnectToDatabase = [
            'message' => 'Cannot connect to database',
            'test' => function() {
                $database = Database::getInstance();
                $database->connect();
            }
        ];
        
        $areEntitiesDeclared = [
            'message' => 'No entities were found in ' . ShellProgram::ENTITIES_DIR . ' directory',
            'test' => function() {
                if(!is_dir(ShellProgram::ENTITIES_DIR)) throw new Exception('Directory : ' . ShellProgram::ENTITIES_DIR . ' does not exits. Maybe you don\'t have entities to link together');
            }
        ];

        $areMultipleEntitiesDeclared = [
            'message' => 'Impossible to link entities together because only one Entity has been created',
            'test' => function() {
                $entities = array_diff(scandir(ShellProgram::ENTITIES_DIR), array('..', '.'));
                $entities  = array_map(fn($entity) => new PascalCaseWord(pathinfo($entity, PATHINFO_FILENAME)), $entities);

                if(count($entities) === 1) throw new Exception('Only one entity (' . implode(', ', $entities) . ') was found in directory : ' . ShellProgram::ENTITIES_DIR);
                if(count($entities) === 0) throw new Exception('No entity was found in directory : ' . ShellProgram::ENTITIES_DIR);
            }
        ];

        $isDatabaseUpToDateWithEntities = [

        ];

        return [
            new Test($canConnectToDatabase['message'], $canConnectToDatabase['test']),
            new Test($areEntitiesDeclared['message'], $areEntitiesDeclared['test']),
            new Test($areMultipleEntitiesDeclared['message'], $areMultipleEntitiesDeclared['test']),
        ];
    }

    public static function execute() : void
    {     
        self::$database = Database::getInstance();
        self::$database->connect();

        $existingEntities = array_diff(scandir(ShellProgram::ENTITIES_DIR), array('..', '.'));
        $existingEntities  = array_map(fn($entity) => new PascalCaseWord(pathinfo($entity, PATHINFO_FILENAME)), $existingEntities);

        $entityName = self::askEntityName(...$existingEntities);
        ShellProgram::addBreakLine();

        $linkedEntityName = self::askLinkedTo($entityName, ...$existingEntities);
        ShellProgram::addBreakLine();

        $relation = self::askRelationType($entityName, $linkedEntityName);
        ShellProgram::addBreakLine();

        $mainEntityPropertyName = self::askRelationPropertyName($relation, $entityName, $linkedEntityName);
       
        ShellProgram::addBreakLine();
        
        $linkedEntityPropertyName = self::askRelationPropertyName($relation, $linkedEntityName, $entityName);

        self::addRelationInEntityFile($entityName, $linkedEntityName, $relation, $mainEntityPropertyName);
        self::addRelationInEntityFile($linkedEntityName, $entityName, $relation->inverse(), $linkedEntityPropertyName);

        self::persistRelation($relation, $entityName, $linkedEntityName);

        self::$database->disconnect();
    }

    private static function askEntityName(PascalCaseWord ...$existingEntities) : PascalCaseWord
    {
        $answer = ShellProgram::askCloseEndedQuestion('Quelle entité souhaitez vous lier ?', $existingEntities);

        return new PascalCaseWord($answer);
    }

    private static function askLinkedTo(PascalCaseWord $chosenEntity, PascalCaseWord ...$existingEntities) : PascalCaseWord
    {
        $linkableEntities = $existingEntities;
        unset($linkableEntities[array_search($chosenEntity, $linkableEntities)]);

        if(empty($linkableEntities)) {
            ShellProgram::displayErrorMessage('Impossible de réaliser une liaison : Aucune autre entité existe. Utilisez : ' . MakeEntity::CMD_NAME . ' pour créer une nouvelle entité.');
            ShellProgram::close();
        }

        $answer = ShellProgram::askCloseEndedQuestion('A quelle entité la liaison se fera ?', $linkableEntities);

        return new PascalCaseWord($answer);
    }

    private static function askRelationType(PascalCaseWord $mainEntity, PascalCaseWord $linkedEntity) : SQLTableRelation
    {
        $oneToMany = SQLTableRelation::ONE_TO_MANY->value;
        $manyToOne = SQLTableRelation::MANY_TO_ONE->value;

        // $manyToMany = SQLTableRelation::MANY_TO_MANY->value;
        // {$manyToMany} : Un(e) ou plusieurs {$mainEntity} sont lié(e)s à un(e) ou plusieurs {$linkedEntity}.

        $helpMessage = <<<HELP
        {$oneToMany} : Un(e) {$mainEntity} est lié(e) à un(e) ou plusieurs {$linkedEntity}. Un(e) ou plusieurs {$linkedEntity} sont lié(e)s à un(e) {$mainEntity}.
        {$manyToOne} : Un(e) ou plusieurs {$mainEntity} sont lié(e)s à un(e) {$linkedEntity}. Un(e) {$linkedEntity} est lié(e) à un(e) ou plusieurs {$mainEntity}.
        HELP;

        $relation = ShellProgram::askCloseEndedQuestion('Quelle relation y a t-il entre ces deux entités ?', [SQLTableRelation::MANY_TO_ONE->value, SQLTableRelation::ONE_TO_MANY->value], $helpMessage);

        return SQLTableRelation::from($relation);
    }

    private static function askRelationPropertyName(SQLTableRelation $relation, PascalCaseWord $entityName, PascalCaseWord $relatedEntityName) : CamelCaseWord
    {
        $answer = ShellProgram::askOpenEndedQuestion('Quelle propriété souhaitez vous rajouter dans l\'entité ' . $entityName . ' pour accéder à la relation créé avec l\'entité ' . $relatedEntityName  . ' ?', defaultAnswer : StringHelper::pascalCaseToCamelCase($relatedEntityName));

        if(!StringHelper::isCamelCase($answer)) {
            ShellProgram::displayErrorMessage($answer . ' doit être formatté en camelCase');
            return call_user_func(__METHOD__, $relation, $entityName, $relatedEntityName);
        }

        return new CamelCaseWord($answer);
    }

    private static function addRelationInEntityFile(PascalCaseWord $entityName, PascalCaseWord $linkedEntityName, SQLTableRelation $relation, CamelCaseWord $propertyName) : void
    {
        $entityNamespace = 'App\\Entities\\' . $entityName;

        $linkedEntityNamespace = 'App\\Entities\\' . $linkedEntityName;
        $linkedEntityFqcn = new FQCN($linkedEntityNamespace);

        $entityFilePath = ShellProgram::ENTITIES_DIR . '/' . $entityName->getValue() . '.php';
        $phpFile = PhpFile::fromCode(file_get_contents($entityFilePath));

        $class = $phpFile->getClasses()[$entityNamespace];

        if($class instanceof ClassType) {
            // Ajouter/remplacer la propriété
            
            if($class->hasProperty($propertyName->getValue())) {
                $class->removeProperty($propertyName->getValue());
            } 

            $property = $class->addProperty($propertyName->getValue());
            
            $type = match($relation) {
                SQLTableRelation::MANY_TO_ONE => $linkedEntityFqcn->getValue(),
                SQLTableRelation::ONE_TO_MANY => DataType::ARRAY->typeDeclaration(),
                default => throw new Exception('This type of relation is not suported by this command')
            };

            $property->setType($type);

            // Créer un setter
            $methodName = 'set' . StringHelper::camelCaseToPascalCase($propertyName)->getValue();

            if($class->hasMethod($methodName)) {
                $class->removeMethod($methodName);
            }

            $class
                ->addMethod($methodName)
                ->setReturnType('self')
                ->addBody(
                    <<<METHOD
                    \$this->{$propertyName} = \${$propertyName};

                    return \$this;
                    METHOD
                )
                ->addParameter($propertyName)
                ->setType($type);

            // Créer un getter
            $methodName = 'get' . StringHelper::camelCaseToPascalCase($propertyName)->getValue();

            if($class->hasMethod($methodName)) {
                $class->removeMethod($methodName);
            }

            $class
                ->addMethod($methodName)
                ->setReturnType($type)
                ->addBody('return $this->' . $propertyName . ';');

            $filePath = ShellProgram::ENTITIES_DIR . '/' .$entityName . '.php';
    
            file_put_contents($filePath, $phpFile);
        }
    }

    private static function persistRelation(SQLTableRelation $relation, PascalCaseWord $mainEntityName, PascalCaseWord $linkedEntityName) : void
    {
        // Modifier la BDD pour créer la relation

        $mainTable = StringHelper::pascalCaseToSnakeCase($mainEntityName);
        $linkedTable = StringHelper::pascalCaseToSnakeCase($linkedEntityName);

        match($relation) {
            SQLTableRelation::MANY_TO_ONE,
            SQLTableRelation::ONE_TO_MANY => self::$database->addForeignkey($mainTable, $linkedTable),
            default => null,
        };
    }
}
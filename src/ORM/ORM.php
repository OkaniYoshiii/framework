<?php

namespace OkaniYoshiii\Framework\ORM;

use Exception;
use OkaniYoshiii\Framework\Database;
use OkaniYoshiii\Framework\Enums\DataType;
use OkaniYoshiii\Framework\Helpers\StringHelper;
use OkaniYoshiii\Framework\Types\Primitive\CamelCaseWord;
use OkaniYoshiii\Framework\Types\Primitive\FQCN;
use ReflectionClass;

class ORM
{
    private readonly Database $database;

    public function __construct()
    {
        $this->database = Database::getInstance();
    }

    public function findEntities(FQCN|string $entityFqcn, string ...$properties) : array
    {   
        if(gettype($entityFqcn) === DataType::STRING) {
            $entityFqcn = new FQCN($entityFqcn);
        }

        $entityReflection = new ReflectionClass($entityFqcn);
        $fields = [];
        foreach($properties as $property)
        {
            if($entityReflection->hasProperty($property)) {
                throw new Exception('Property "' . $property . '" is not a property of Class "' . $entityFqcn . '"');
            }

            if(!StringHelper::isCamelCase($property)) {
                throw new Exception('Property "' . $property . '" need to be formatted in CamelCase');
            }

            $fields[] = new CamelCaseWord($property);
        }

        $table = StringHelper::pascalCaseToSnakeCase($entityFqcn->getClassName());
    }
}
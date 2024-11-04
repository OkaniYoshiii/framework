<?php

namespace OkaniYoshiii\Framework\Contracts\Abstracts;

use Exception;
use OkaniYoshiii\Framework\Contracts\Attributes\SQLField;
use OkaniYoshiii\Framework\Contracts\Attributes\SQLPrimaryKey;
use OkaniYoshiii\Framework\Contracts\Attributes\SQLTable;
use OkaniYoshiii\Framework\Database;
use OkaniYoshiii\Framework\Helpers\StringHelper;
use OkaniYoshiii\Framework\Types\Primitive\FQCN;
use OkaniYoshiii\Framework\Types\Primitive\SnakeCaseWord;
use PDO;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;

abstract class AbstractTable
{
    private readonly Database $database;

    private readonly FQCN $entityFqcn;

    /** @var \ReflectionProperty[] */
    private readonly array $properties;

    /** @var \OkaniYoshiii\Framework\Contracts\Attributes\SQLTable */
    private readonly SQLTable $sqlTable;

    public function __construct()
    {
        $this->database = Database::getInstance();
        $this->database->connect();
        $this->entityFqcn = $this->configureEntityFqcn();
        $this->properties = (new ReflectionClass((string) $this->entityFqcn))->getProperties();

        $sqlTables = $this->getSQLTablesFromEntity();

        if(count($sqlTables) !== 1) throw new Exception('Entity ' . $this->entityFqcn . ' must have one and only one ' . SQLField::class . ' attribute(s) defined.');

        $this->sqlTable = $sqlTables[0];
    }

    abstract protected function configureEntityFqcn() : FQCN;

    public function findAll() : array
    {
        $stmt = $this->database->query('SELECT * FROM ' . $this->sqlTable);
        $stmt->setFetchMode(PDO::FETCH_CLASS, $this->entityFqcn);
        return $stmt->fetchAll();
    }

    public function createOne(Entity $entity) : void
    {
        $this->checkIfInstanceOfEntityFQCN($entity);

        $values = [];
        $parameters = [];
        $fieldsName = [];
        foreach($this->properties as $property)
        {
            $sqlFieldAttributes = $property->getAttributes(SQLField::class);

            if(count($sqlFieldAttributes) > 1) throw new Exception('Property : ' . $property->getName() . ' of class ' . $this->entityFqcn . ' must have one or zero ' . SQLField::class . ' Attribute. Found ' . count($sqlFieldAttributes));
            
            if(count($sqlFieldAttributes) === 1) {
                $sqlField = $sqlFieldAttributes[0]->newInstance();
                $method = 'get' . StringHelper::snakeCaseToPascalCase(new SnakeCaseWord($sqlField->getName()));
                if(method_exists($entity, $method)) {
                    $fieldsName[] = $this->sqlTable->getName() . '.' . $sqlField->getName();
                    $values[] = $entity->{$method}();
                    $parameters[] = ':' . strtolower($this->sqlTable->getName() . '_' . $sqlField->getName());
                } else {
                    throw new Exception('Entity ' . $entity::class . ' must have a method named ' . $method . ' to access its related property');
                }
            }
        }

        $sql = 'INSERT INTO ' . $this->sqlTable->getName() . '(' . implode(', ', $fieldsName) .  ')' . ' VALUES (' . implode(', ', $parameters) . ')';
        var_dump($sql);

        $stmt = $this->database->prepare($sql);

        foreach($parameters as $index => $parameter) {
            $stmt->bindValue($parameter, $values[$index]);
        }

        $stmt->execute();
    }

    public function updateOne(Entity $entity) : void
    {
        $this->checkIfInstanceOfEntityFQCN($entity);

        $values = [];
        $parameters = [];
        $fieldsName = [];
        $sets = [];
        $primaryKeyProperties = [];
        foreach($this->properties as $property)
        {
            $sqlFieldAttributes = $property->getAttributes(SQLField::class);

            if(count($sqlFieldAttributes) > 1) throw new Exception('Property : ' . $property->getName() . ' of class ' . $this->entityFqcn . ' must have one or zero ' . SQLField::class . ' Attribute. Found ' . count($sqlFieldAttributes));
            
            if(!empty($sqlFieldAttributes)) {
                $sqlField = $sqlFieldAttributes[0]->newInstance();
                $method = 'get' . StringHelper::snakeCaseToPascalCase(new SnakeCaseWord($sqlField->getName()));
                if(method_exists($entity, $method)) {
                    $parameter = ':' . strtolower($this->sqlTable->getName() . '_' . $sqlField->getName());
                    
                    $fieldsName[] = $this->sqlTable->getName() . '_' . $sqlField->getName();
                    $values[] = $entity->{$method}();
                    $sets[] = $this->sqlTable->getName() . '.' . $sqlField->getName() . '=' . $parameter;
                    $parameters[] = $parameter;
                } else {
                    throw new Exception('Entity ' . $entity::class . ' must have a method named ' . $method . ' to access its related property');
                }
            }

            $sqlPrimaryKeyAttributes = $property->getAttributes(SQLPrimaryKey::class);

            if(!empty($sqlPrimaryKeyAttributes)) {
                $primaryKeyProperties[] = $property;
            }
        }

        $propertyName = $primaryKeyProperties[0]->getName();
        $primaryKeyGetter = 'get' . ucfirst($propertyName);
        $primaryKeyName = $primaryKeyProperties[0]->getAttributes(SQLPrimaryKey::class)[0]->newInstance()->getName();
        $parameter = $this->fieldToQueryParameter($primaryKeyName);
        $where = $primaryKeyName . '=' . $parameter;
        $values[] = $entity->{$primaryKeyGetter}();
        $parameters[] = $parameter;

        $sql = 'UPDATE ' . $this->sqlTable->getName() . ' SET ' . implode(', ', $sets) . ' WHERE ' . $where;
        var_dump($sql, $values, $parameters);
        $stmt = $this->database->prepare($sql);
        foreach($parameters as $index => $parameter)
        {
            $stmt->bindValue($parameter, $values[$index]);
        }
        $stmt->execute();
    }

    public function deleteOne(int $id) : void
    {
        $primaryKeyProperty = array_filter($this->properties, fn($property) => (!empty($property->getAttributes(SQLPrimaryKey::class))))[0];
        $field = $primaryKeyProperty->getAttributes(SQLPrimaryKey::class)[0]->newInstance()->getName();
        $parameter = $this->fieldToQueryParameter($field);
        $sql = 'DELETE FROM ' . $this->sqlTable->getName() . ' WHERE ' . $field . ' = ' . $parameter;
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue($parameter, $id);
        $stmt->execute();
    }

    private function fieldToQueryParameter(string $name) : string
    {
        return ':' . $this->sqlTable->getName() . '_' . $name;
    }

    private function getPropertiesHavingSQLFieldAttribute(FQCN $entityFqcn) : array
    {
        $entityReflection = new ReflectionClass((string) $entityFqcn);

        $sqlProperty = [];
        foreach($entityReflection->getProperties() as $reflectionProperty)
        {
            $sqlFieldAttributes = $reflectionProperty->getAttributes(SQLField::class);

            foreach($sqlFieldAttributes as $sqlFieldAttribute)
            {
                $sqlProperty[] = $sqlFieldAttribute->newInstance();
            }
        }

        return $sqlProperty;
    }

    /**
     * @return \OkaniYoshiii\Framework\Contracts\Attributes\SQLTable[]
     */
    private function getSQLTablesFromEntity() : array
    {
        $entityReflection = new ReflectionClass((string) $this->entityFqcn);

        return array_map(fn(ReflectionAttribute $reflectionAttribute) => $reflectionAttribute->newInstance(), $entityReflection->getAttributes(SQLTable::class));
    }

    /**
     * @return \OkaniYoshiii\Framework\Contracts\Attributes\SQLPrimaryKey[]
     */
    private function getSQLPrimaryKeysFromEntity(FQCN $entityFqcn) : array
    {
        $entityReflection = new ReflectionClass((string) $entityFqcn);

        $sqlPrimaryKeys = [];
        foreach($entityReflection->getProperties() as $reflectionProperty)
        {
            $attributes = $reflectionProperty->getAttributes(SQLPrimaryKey::class);

            foreach($attributes as $attribute)
            {
                $sqlPrimaryKeys[] = $attribute->newInstance();
            }
        }

        return $sqlPrimaryKeys;
    }

    /**
     * @return \OkaniYoshiii\Framework\Contracts\Attributes\SQLField[]
     */
    private function getSQLFieldsFromEntity(FQCN $entityFqcn) : array
    {
        $entityReflection = new ReflectionClass((string) $entityFqcn);

        $sqlFields = [];
        foreach($entityReflection->getProperties() as $reflectionProperty)
        {
            $sqlFieldAttributes = $reflectionProperty->getAttributes(SQLField::class);

            foreach($sqlFieldAttributes as $sqlFieldAttribute)
            {
                $sqlFields[] = $sqlFieldAttribute->newInstance();
            }
        }

        return $sqlFields;
    }

    protected function checkIfInstanceOfEntityFQCN(Entity $entity) : void
    {
        if($entity::class !== $this->entityFqcn->getValue()) {
            throw new Exception($entity::class . ' is not an instance of $entityFqcn : ' . $this->entityFqcn->getValue());
        }
    }
}
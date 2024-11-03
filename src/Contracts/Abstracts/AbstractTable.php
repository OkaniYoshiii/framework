<?php

namespace OkaniYoshiii\Framework\Contracts\Abstracts;

use Exception;
use OkaniYoshiii\Framework\Contracts\Attributes\SQLField;
use OkaniYoshiii\Framework\Contracts\Attributes\SQLTable;
use OkaniYoshiii\Framework\Database;
use OkaniYoshiii\Framework\Types\Entity;
use OkaniYoshiii\Framework\Types\Primitive\FQCN;
use PDO;
use ReflectionClass;
use ReflectionProperty;

abstract class AbstractTable
{
    private readonly Database $database;
    private readonly SQLTable $table;
    private readonly FQCN $entityFqcn;
    private readonly array $sqlFields;

    protected function __construct()
    {
        $this->database = Database::getInstance();
        $this->entityFqcn = $this->configureEntityFqcn();

        $tables = $this->getSQLTablesFromEntity();

        if(count($tables) !== 1) throw new Exception('Entity ' . $this->entityFqcn . ' must have one and only one ' . SQLField::class . ' attribute(s) defined.');

        $this->table = $tables[0];
        $this->sqlFields = $this->getSQLFieldsFromEntity($this->entityFqcn);
    }

    abstract protected function configureEntityFqcn() : FQCN;

    public function findAll() : array
    {
        $stmt = $this->database->query('SELECT * FROM ' . $this->table);
        $stmt->setFetchMode(PDO::FETCH_CLASS, $this->entityFqcn);
        return $stmt->fetchAll();
    }

    public function createOne(Entity $entity) : void
    {
        if($entity::class !== $this->entityFqcn->getValue()) {
            throw new Exception('Argument "$entity" is not an instance of $entityFqcn : ' . $this->entityFqcn->getValue());
        }

        $stmt = $this->database->query('INSERT INTO ' . $this->table . '(' . '' .  ')' . ' VALUES (' . '' . ')');

    }

    public function updateOne() : void
    {
        
    }

    public function deleteOne() : void
    {

    }

    private function getSQLTablesFromEntity() : array
    {
        $entityReflection = new ReflectionClass((string) $this->entityFqcn);

        return $entityReflection->getAttributes(SQLTable::class);
    }

    private function getSQLFieldsFromEntity(FQCN $entityFqcn) : array
    {
        $entityReflection = new ReflectionClass((string) $entityFqcn);
        
        return array_map(fn(ReflectionProperty $property) => $property->getAttributes(SQLField::class), $entityReflection->getProperties());
    }
}
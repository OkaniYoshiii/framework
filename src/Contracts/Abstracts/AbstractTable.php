<?php

namespace OkaniYoshiii\Framework\Contracts\Abstracts;

use Exception;
use OkaniYoshiii\Framework\Database;
use OkaniYoshiii\Framework\Types\Entity;
use OkaniYoshiii\Framework\Types\Primitive\FQCN;
use PDO;

abstract class AbstractTable
{
    private readonly Database $database;
    private readonly string $table;
    private readonly FQCN $entityFqcn;

    protected function __construct()
    {
        $this->database = Database::getInstance();
        $this->table = $this->configureTableName();
        $this->entityFqcn = $this->configureEntityFqcn();
    }

    abstract protected function configureTableName() : string;
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
}
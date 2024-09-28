<?php

namespace Framework;

use Framework\Contracts\Traits\SingletonTrait;
use PDO;
use PDOStatement;

class Database
{
    use SingletonTrait;

    private ?PDO $pdo = null;
    private ?PDOStatement $stmt = null;

    public function connectAsAdmin() : void
    {
        $this->pdo = new PDO('mysql:host=' . $_ENV['DATABASE_HOST'], $_ENV['DATABASE_USER'], $_ENV['DATABASE_PASSWORD']);
    }

    public function connect()
    {
        $this->pdo = new PDO('mysql:host=' . $_ENV['DATABASE_HOST'] . ';dbname=' . $_ENV['DATABASE_NAME'], $_ENV['DATABASE_USER'], $_ENV['DATABASE_PASSWORD']);
    }

    public function create() 
    {
        $this->stmt = $this->pdo->query('CREATE DATABASE IF NOT EXISTS ' . $_ENV['DATABASE_NAME']);
    }

    public function createTable(string $table, string ...$fields) : void
    {
        $sqlQuery = 'CREATE TABLE IF NOT EXISTS ' . $table . '(' . implode(', ', $fields) . ')';
        $pdo = $this->getPdo();
        $pdo->query($sqlQuery);
    }

    public function disconnect()
    {
        $this->pdo = null;
        $this->stmt = null;
    }

    /**
     * Get the value of pdo
     */ 
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * Set the value of stmt
     *
     * @return  self
     */ 
    public function setStmt($stmt)
    {
        $this->stmt = $stmt;

        return $this;
    }
}
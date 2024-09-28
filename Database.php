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
        $this->pdo->query($sqlQuery);
    }

    public function tableExists(string $table) : bool
    {
        $sqlQuery = 'SHOW TABLES LIKE :table';
        $this->stmt = $this->pdo->prepare($sqlQuery);
        $this->stmt->bindValue(':table', $table, PDO::PARAM_STR);
        $this->stmt->execute();
        $result = $this->stmt->fetch();

        return ($result !== false);
    }

    public function getTables() : array
    {
        $sqlQuery = 'SHOW TABLES';
        $this->stmt = $this->pdo->query($sqlQuery);
        $results = $this->stmt->fetchAll(PDO::FETCH_COLUMN);

        return $results;
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
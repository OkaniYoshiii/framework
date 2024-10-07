<?php

namespace OkaniYoshiii\Framework;

use OkaniYoshiii\Framework\Contracts\Traits\SingletonTrait;
use OkaniYoshiii\Framework\Types\EntityDto;
use OkaniYoshiii\Framework\Types\Primitive\SnakeCaseWord;
use OkaniYoshiii\Framework\Types\Primitive\Word;
use PDO;
use PDOStatement;

class Database
{
    use SingletonTrait;

    private ?PDO $pdo = null;
    private ?PDOStatement $stmt = null;

    private readonly Word $name;
    private readonly Word $host;
    private readonly Word $user;
    private readonly string $password;

    private function __construct()
    {
        App::loadEnvVariables();

        $this->name = new Word($_ENV['DATABASE_NAME']);
        $this->host = new Word($_ENV['DATABASE_HOST']);
        $this->user = new Word($_ENV['DATABASE_USER']);
        $this->password = $_ENV['DATABASE_PASSWORD'];
    }

    public function connectAsAdmin() : void
    {
        $this->pdo = new PDO('mysql:host=' . $this->host, $this->user, $this->password);
    }

    public function connect()
    {
        $this->pdo = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->name, $this->user, $this->password);
    }

    public function create() 
    {
        $this->stmt = $this->pdo->query('CREATE DATABASE IF NOT EXISTS ' . $this->name);
    }

    public function createTable(SnakeCaseWord $table, SnakeCaseWord ...$fields) : void
    {
        $sqlQuery = 'CREATE TABLE IF NOT EXISTS ' . $table . '(' . implode(', ', $fields) . ')';
        $this->pdo->query($sqlQuery);
    }

    public function tableExists(SnakeCaseWord $table) : bool
    {
        $sqlQuery = 'SHOW TABLES LIKE :table';
        $this->stmt = $this->pdo->prepare($sqlQuery);
        $this->stmt->bindValue(':table', $table->getValue(), PDO::PARAM_STR);
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

    public function addForeignkey(EntityDto $entity, EntityDto $linkedEntity) : void
    {
        $sqlQuery = 'ALTER TABLE ' . $entity->getTable() . ' ADD ' . $linkedEntity->getPrimarykey() . ' INT(11) UNSIGNED NOT NULL;';
        $sqlQuery .= 'ALTER TABLE ' . $entity->getTable() . ' ADD FOREIGN KEY (' . $linkedEntity->getPrimarykey() . ') REFERENCES ' .  $linkedEntity->getTable() . '(' . $linkedEntity->getPrimarykey() . ');';
        
        self::$pdo->query($sqlQuery);
    }

    public function disconnect()
    {
        $this->pdo = null;
        $this->stmt = null;
    }
}
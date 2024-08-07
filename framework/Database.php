<?php

namespace Framework;

use App\Traits\SingletonTrait;
use PDO;
use PDOStatement;

class Database
{
    use SingletonTrait;

    private ?PDO $pdo = null;
    private ?PDOStatement $stmt = null;

    public function connect()
    {
        $config = Config::getInstance();
        $config = $config->get('local', 'database');

        $this->pdo = new PDO($config['dsn'], $config['username'], $config['password']);
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
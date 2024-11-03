<?php

namespace OkaniYoshiii\Framework\Types\Database;

use PDO;

final class SQLColumnMetadata
{
    public function __construct(
        private readonly array $flags,
        private readonly string $name,
        private readonly string $table,
        private readonly string $len,
        private readonly string $precision,
        private readonly string $pdoType,
    ){}

    /**
     * Get the value of flags
     */ 
    public function getFlags() : array
    {
        return $this->flags;
    }

    /**
     * Get the value of name
     */ 
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Get the value of table
     */ 
    public function getTable() : string
    {
        return $this->table;
    }

    /**
     * Get the value of len
     */ 
    public function getLen() : int
    {
        return $this->len;
    }

    /**
     * Get the value of precision
     */ 
    public function getPrecision() : int
    {
        return $this->precision;
    }

    /**
     * Get the value of pdoType
     */ 
    public function getPdoType() : int
    {
        return $this->pdoType;
    }
}
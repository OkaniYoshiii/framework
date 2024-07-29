<?php

namespace App\Entities;

use ReflectionClass;

class Entity
{
    private int $id;
    private static string $tableName;
    private static string $primaryKey;
    private static array $properties;

    public function getId() : int
    {
        return $this->id;
    }

    public function setId(int $value) : void
    {
        $this->id = $value;
    }

    public function getProperties()
    {
        if(!isset($properties)) {
            $reflection = new ReflectionClass($this);
            self::$properties = $reflection->getProperties();
        }

        return self::$properties;
    }
}
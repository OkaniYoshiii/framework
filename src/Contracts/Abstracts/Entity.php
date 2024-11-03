<?php

namespace OkaniYoshiii\Framework\Types\Database;

abstract class Entity
{
    public function getClassVars() : array
    {
        return get_class_vars($this::class);
    }
}
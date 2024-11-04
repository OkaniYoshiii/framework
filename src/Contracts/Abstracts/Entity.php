<?php

namespace OkaniYoshiii\Framework\Contracts\Abstracts;

abstract class Entity
{
    public function getClassVars() : array
    {
        return get_class_vars($this::class);
    }
}
<?php

namespace OkaniYoshiii\Framework\ORM;

abstract class SQLStatement
{
    public function __toString()
    {
        
    }

    abstract public function validate() : void;
}
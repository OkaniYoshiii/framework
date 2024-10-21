<?php

namespace OkaniYoshiii\Framework\Types;

use OkaniYoshiii\Framework\Enums\SQLFieldType;
use OkaniYoshiii\Framework\Types\Primitive\SnakeCaseWord;

class SQLPrimaryKey extends SQLField
{
    public function __construct(SnakeCaseWord $name)
    {
        $name = new SnakeCaseWord($name . '_id');
        parent::__construct($name, SQLFieldType::INTEGER, isNullable : true);

        $this->setIsUnsigned(true);
        $this->setLength(11);
        $this->setIsPrimaryKey(true);
    }
}
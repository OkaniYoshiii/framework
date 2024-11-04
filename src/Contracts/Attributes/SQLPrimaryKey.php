<?php

namespace OkaniYoshiii\Framework\Contracts\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class SQLPrimaryKey
{
    public function __construct(
        private string $name
    ){}

    public function getName() : string
    {
        return $this->name;
    }
}
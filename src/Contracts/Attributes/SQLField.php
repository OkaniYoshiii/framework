<?php

namespace OkaniYoshiii\Framework\Contracts\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class SQLField
{
    public function __construct(
        private string $name,
        private bool $isNullable
    ){}

    public function getName() : string
    {
        return $this->name;
    }

    public function getIsNullable() : bool
    {
        return $this->isNullable;
    }
}
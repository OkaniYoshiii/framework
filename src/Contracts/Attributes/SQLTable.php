<?php

namespace OkaniYoshiii\Framework\Contracts\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class SQLTable
{
    public function __construct(
        private string $name,
    ){}

    public function getName() : string
    {
        return $this->name;
    }
}
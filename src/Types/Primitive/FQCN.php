<?php

namespace OkaniYoshiii\Framework\Types\Primitive;

use InvalidArgumentException;

class FQCN extends StringType
{
    public function validate(string $value) : void
    {
        if(!class_exists($value)) {
            throw new InvalidArgumentException($value . ' class does not exists or is not a valid Fully Qualified Class Name');
        }
    }
}
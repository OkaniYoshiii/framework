<?php

namespace OkaniYoshiii\Framework\Types\Primitive;

use Exception;

class FQCN extends StringType
{
    public function validate(string $value) : void
    {
        if(!class_exists($value)) {
            throw new Exception($value . ' class does not exists, has not been correctly autoloaded or is not a valid Fully Qualified Class Name');
        }
    }
}
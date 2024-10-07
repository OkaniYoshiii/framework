<?php

namespace OkaniYoshiii\Framework\Types\Primitive;

use Exception;
use OkaniYoshiii\Framework\Helpers\StringHelper;
use OkaniYoshiii\Framework\Types\StringType;

class CamelCaseWord extends Word
{
    protected function validate(string $value): void
    {
        parent::validate($value);
        
        if(!StringHelper::isCamelCase($value)) throw new Exception('"' . $value . '" is not a camelCase word.');
    }
}
<?php

namespace OkaniYoshiii\Framework\Types\Primitive;

use Exception;
use OkaniYoshiii\Framework\Helpers\StringHelper;

class Word extends StringType
{
    protected function validate(string $value): void
    {
        if(!StringHelper::isWord($value)) throw new Exception('"' . $value . '" is not a word.');
    }
}
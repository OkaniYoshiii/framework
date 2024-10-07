<?php

namespace OkaniYoshiii\Framework\Types\Primitive;

use Exception;
use OkaniYoshiii\Framework\Helpers\StringHelper;
use OkaniYoshiii\Framework\Types\StringType;

class Word extends StringType
{
    protected function validate(string $value): void
    {
        if(!StringHelper::isWord($value)) throw new Exception('"' . $value . '" is not a word.');
    }
}
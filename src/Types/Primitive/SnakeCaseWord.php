<?php

namespace OkaniYoshiii\Framework\Types\Primitive;

use Exception;
use OkaniYoshiii\Framework\Helpers\StringHelper;
use OkaniYoshiii\Framework\Types\StringType;

class SnakeCaseWord extends Word
{
    protected function validate(string $value): void
    {
        parent::validate($value);

        if(!StringHelper::isSnakeCase($value)) throw new Exception('"' . $value . '" is not a snake_case word.');
    }
}
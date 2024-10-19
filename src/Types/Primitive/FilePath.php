<?php

namespace OkaniYoshiii\Framework\Types\Primitive;

use Exception;

class FilePath extends StringType
{
    public function validate(string $value) : void
    {
        if(!file_exists($value)) {
            throw new Exception($value . ' is not a valid file path : file does not exists');
        }
    }
}

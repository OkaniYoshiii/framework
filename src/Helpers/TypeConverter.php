<?php

namespace OkaniYoshiii\Framework\Helpers;

use Exception;
use OkaniYoshiii\Framework\Enums\DataType;
use PDO;

final class TypeConverter
{
    public static function pdoTypeToPhpType(int $pdoType) : ?DataType
    {
        return match($pdoType) {
            PDO::PARAM_BOOL => DataType::BOOLEAN,
            PDO::PARAM_STR => DataType::STRING,
            PDO::PARAM_INT => DataType::INTEGER,
            PDO::PARAM_NULL => null,
            default => throw new Exception('Argument "$pdoType" does not correspond to any PDO::PARAM_* constants')
        };
    }
}
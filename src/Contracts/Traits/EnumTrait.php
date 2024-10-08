<?php

namespace OkaniYoshiii\Framework\Contracts\Traits;

trait EnumTrait
{
    public static function values() : array
    {
        return array_column(self::cases(), 'value');
    }
}
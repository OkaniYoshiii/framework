<?php

namespace OkaniYoshiii\Framework\Enums;

use OkaniYoshiii\Framework\Contracts\Traits\EnumTrait;

enum SQLTableRelation : string
{
    use EnumTrait;

    case MANY_TO_ONE = 'ManyToOne';
    case MANY_TO_MANY = 'ManyToMany';
    case ONE_TO_MANY = 'OneToMany';

    public function inverse() : ?self
    {
        return match($this) {
            self::MANY_TO_ONE => self::ONE_TO_MANY,
            self::ONE_TO_MANY => self::MANY_TO_ONE,
            default => null
        };
    }
}
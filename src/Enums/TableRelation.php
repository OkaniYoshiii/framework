<?php

namespace OkaniYoshiii\Framework\Enums;

use OkaniYoshiii\Framework\Contracts\Traits\EnumTrait;

enum TableRelation : string
{
    use EnumTrait;

    case MANY_TO_ONE = 'ManyToOne';
    case MANY_TO_MANY = 'ManyToMany';
    case ONE_TO_MANY = 'OneToMany';
}
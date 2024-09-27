<?php

namespace Framework\Enums;

enum DataType : string
{
    case STRING = 'string';
    case BOOLEAN = 'boolean';
    case ARRAY = 'array';
    case OBJECT = 'object';
}
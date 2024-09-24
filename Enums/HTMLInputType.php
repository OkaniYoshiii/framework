<?php

namespace Framework\Enums;

enum HTMLInputType : string
{
    case DATE = 'date';
    case DATETIME_LOCAL = 'datetime-local';
    case EMAIL = 'email';
    case NUMBER = 'number';
    case PASSWORD = 'password';
    case TEXT = 'text';
    case HIDDEN = 'hidden';
    case SUBMIT = 'submit';
}
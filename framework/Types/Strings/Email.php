<?php

namespace Framework\Types\Strings;

use Exception;
use Stringable;

class Email implements Stringable
{
    private string $value;

    public function __construct(string $email)
    {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception($email . ' is not a valid email.');

        $this->value = $email;
    }

    public function __toString()
    {
        return $this->value;
    }
}
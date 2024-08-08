<?php

namespace Framework\Types\Strings;

use Exception;
use Stringable;

class Password implements Stringable
{
    private int $minLength = 8;
    private bool $mustHaveSpecialChars = false;
    private bool $mustHaveNumbers = false;
    private bool $mustHaveCapitalLetters = false;

    private string $value;

    public function __construct(string $password)
    {
        if($this->minLength > strlen($password)) throw new Exception('Password need to be at least ' . $this->minLength . ' characters long.');
        if($this->mustHaveSpecialChars && !preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $password)) throw new Exception('Password must contain at least one special character.');
        if($this->mustHaveNumbers && !preg_match('/[0-9]/', $password)) throw new Exception('Password must contain at least one number.');
        if($this->mustHaveCapitalLetters && !preg_match('/[A-Z]/', $password)) throw new Exception('Password must contain at least one capital letter.');

        $this->value = $password;
    }

    public function __toString()
    {
        return $this->value;
    }
}
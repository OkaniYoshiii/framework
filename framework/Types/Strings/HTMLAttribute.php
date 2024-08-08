<?php

declare(strict_types=1);

namespace Framework\Types\Strings;

use Stringable;

class HTMLAttribute implements Stringable
{
    private string $name;
    private string $value;

    public function __construct(string $name, string $value = '')
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function __toString()
    {
        return (empty($this->value)) ? $this->name : $this->name . '="' . $this->value . '"';
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getValue() : string
    {
        return $this->value;
    }
}
<?php

namespace Framework\Types\Composed\HTMLElements;

use Framework\Types\Collections\HTMLAttributeCollection;

class HTMLLabelElement extends HTMLElement
{
    private string $value;

    public function __construct(string $value, HTMLAttributeCollection $attributes)
    {
        parent::__construct($attributes);
        
        $this->value = $value;
    }

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }
}
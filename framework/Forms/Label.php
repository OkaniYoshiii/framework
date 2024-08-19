<?php

namespace Framework\Forms;

use Framework\Views\LabelView;

class Label extends HTMLElement
{
    private string $value = '';
    private Input $input;

    public function __construct(Input $input)
    {
        $this->input = $input;
    }

    public function getInput() : Input
    {
        return $this->input;
    }

    public function setValue(string $value) : self
    {
        $this->value = $value;

        return $this;
    }

    public function getValue() : string
    {
        return $this->value;
    }
}
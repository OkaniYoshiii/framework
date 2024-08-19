<?php

namespace Framework\Views;

use Framework\Forms\Label;

class LabelView extends AbstractView
{
    public function __construct(private Label $label)
    {
    }

    public function render() : void
    {
        echo $this->__toString();
    }

    public function __toString(): string
    {
        return (empty($this->label->getValue())) ? '' : '<label for="' . $this->label->getInput()->getId() . '" ' . $this->label->getAttributesAsString() . '>' . $this->label->getValue() . '</label>';
    }
}
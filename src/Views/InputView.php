<?php

namespace OkaniYoshiii\Framework\Views;

use Exception;
use OkaniYoshiii\Framework\Forms\Input;

class InputView extends AbstractView
{
    private Input $input;

    public function __construct(Input $input)
    {
        $this->input = $input;
    }

    public function getSelf() : string
    {
        return '<input type="' . $this->input->getType()->value . '" name="' . $this->input->getName() . '" ' .  'id="' . $this->input->getId() . '" ' . $this->input->getAttributesAsString() . '>';
    }

    public function __toString(): string
    {
        return $this->input->getLabel()->__toString() . $this->getSelf();
    }

    public function render(string $part = 'all') : void
    {
        $possibleValues = ['all', 'self'];

        echo match($part) {
            'all' => $this->__toString(),
            'self' => $this->getSelf(),
            default => throw new Exception('Argument $part of ' . __METHOD__ . ' is invalid. Possible values are : ' . implode(', ', $possibleValues)),
        };
    }
}
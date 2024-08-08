<?php

namespace Framework\Types\Composed\HTMLElements;

abstract class VoidElement extends HTMLElement
{  
    public function __toString(): string
    {
        return '<' . $this->tagName . ' ' . implode(' ', $this->attributes->toArray()) . '>';
    }

    abstract protected function defineTagName() : void;
}
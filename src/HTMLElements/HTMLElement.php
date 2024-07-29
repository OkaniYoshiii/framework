<?php

namespace App\HTMLElements;

class HTMLElement
{
    private array $attributes;
    private array $children;

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function getAttribute(string $name) : ?string
    {
        return $this->attributes[$name] ?? null;
    }

    public function setAttribute(string $name, string $value) : self
    {
        $this->attributes[$name] = $value;

        return $this;
    }    

    public function getAttributes() : array
    {
        return $this->attributes;
    }

    public function removeAttribute(string $name) : self
    {
        unset($this->attributes[$name]);

        return $this;
    }

    public function addChild(HTMLElement $htmlElement) : self
    {
        $this->children[] = $htmlElement;

        return $this;
    }

    public function getChildren() : array
    {
        return $this->children;
    }

    public function removeChild(int $key) : void
    {
        unset($this->children[$key]);
    }
}
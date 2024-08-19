<?php

namespace Framework\Forms;

use Framework\Views\AbstractView;

abstract class HTMLElement
{
    protected string $id = '';
    protected array $attributes = [];
    protected AbstractView $view;

    public function __toString() : string
    {
        $this->initializeView();

        return $this->view->__toString();
    }

    public function render(string $part = 'all') : void
    {
        $this->initializeView();
        
        $this->view->render($part);
    }

    abstract protected function initializeView() : void;
    
    // A tester
    // protected function initializeView() : void
    // {
    //     $viewClass = self::class . 'View';

    //     new $viewClass($this);
    // }

    public function setAttributes(array $attributes) : static
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function setAttribute(string $name, string|bool|float $value) : static
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    public function getAttribute(string $name) : ?string
    {
        return $this->attributes[$name] ?? null;
    }

    public function getAttributes() : array
    {
        return $this->attributes;
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getAttributesAsString() : string
    {
        $attributes = array_map(function($key, $value) {
            if($value === false) return '';
            if($value === true) $value = '';

            if(is_numeric($value)) $value = (string) $value;
            
            return $key . '="' . $value . '"';
        }, array_keys($this->attributes), $this->attributes);

        return implode('', $attributes);
    }
}
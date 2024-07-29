<?php

namespace App\Forms;

class Form
{
    private string $method = 'POST';
    private string $action = '';
    private array $inputs;
    private array $attributes;
    private FormInput $csrfToken;

    public function __construct()
    {
        $this->csrfToken = new FormInput('csrf_token', 'hidden', null, ['value' => 1235]);
    }

    public function setMethod($method) : self
    {
        $this->method = $method;

        return $this;
    }

    public function setAction($action) : self
    {
        $this->action = $action;

        return $this;
    }    

    public function addAttribute(string $name, string $value) : self
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    public function removeAttribute(string $name) : self
    {
        unset($this->attributes[$name]);
        
        return $this;
    }

    public function addInput(string $name, string $type, string $label, array $attributes = []) : self
    {
        $this->inputs[$name] = new FormInput($name, $type, $label, $attributes);

        return $this;
    }

    public function removeInput(string $name) : self
    {
        unset($this->inputs[$name]);

        return $this;
    }

    public function getAttribute(string $name) : string
    {
        return $this->attributes[$name];
    }

    public function getInput(string $name) : FormInput
    {
        return $this->inputs[$name];
    }
 
    public function getAction()
    {
        return $this->action;
    }
    
    public function getMethod()
    {
        return $this->method;
    }

    public function getInputs()
    {
        return $this->inputs;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getCsrfToken()
    {
        return $this->csrfToken;
    }
}
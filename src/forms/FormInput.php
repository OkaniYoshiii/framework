<?php

namespace App\Forms;

class FormInput
{
    private string $name;
    private string $type;
    private string $label;
    private array $attributes;

    public function __construct(string $name, string $type, ?string $label = null, array $attributes = [])
    {
        $this->name = $name;
        $this->type = $type;
        $this->label = is_null($label) ? '' : $label;
        $this->attributes = $attributes;
    }

    public function __toString()
    {
        $htmlAttributes = '';
        foreach($this->attributes as $name => $value)
        {
            $htmlAttributes .= $name . '=' . $value . ' ';
        }
            
        return '<input type="' .  $this->type . '" name="' . $this->name . '" ' . $htmlAttributes . '>';
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of type
     */ 
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the value of attributes
     */ 
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get the value of label
     */ 
    public function getLabel()
    {
        return $this->label;
    }
}
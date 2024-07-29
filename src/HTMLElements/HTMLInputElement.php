<?php

namespace App\HTMLElements;

use App\Enums\HTMLInputType;

class HTMLInputElement extends HTMLElement
{
    private string $name;
    private string $type;
    private string $label;
    private array $attributes;

    public function __construct(string $name, HTMLInputType $type, ?string $label = null, array $attributes = [])
    {
        parent::__construct($attributes);
        
        $this->name = $name;
        $this->type = $type->value;
        $this->label = is_null($label) ? '' : $label;

        $this->attributes['required'] = $attributes['required'] ?? true;
        $this->attributes['disabled'] = $attributes['disabled'] ?? false;
        $this->attributes['readonly'] = $attributes['readonly'] ?? false;
    }

    public function __toString()
    {
        $htmlAttributes = '';
        foreach($this->attributes as $name => $value)
        {
            if($value === false) continue;
            $htmlAttributes .= $name;
            if($value === true) continue;
            $htmlAttributes .= '=' . $value . ' ';
        }
            
        return '<input type="' .  $this->type . '" name="' . $this->name . '" ' . $htmlAttributes . '>';
    }

    public function getIsRequired() : bool
    {
        return $this->attributes['required'];
    }

    public function getIsDisabled() : bool
    {
        return $this->attributes['disabled'];
    }

    public function getIsReadOnly() : bool
    {
        return $this->attributes['readonly'];
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
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * Get the value of label
     */ 
    public function getLabel()
    {
        return $this->label;
    }
}
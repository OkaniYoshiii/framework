<?php

declare(strict_types=1);

namespace Framework\Types\Composed\HTMLElements;

use Framework\Enums\HTMLInputType;
use Framework\Exceptions\CauseEffectException;
use Framework\Types\Collections\HTMLAttributeCollection;
use Framework\Types\Strings\HTMLAttribute;

class HTMLInputElement extends VoidElement
{
    private string $name;
    private string $type;
    private ?HTMLLabelElement $label = null;

    public function __construct(string $name, HTMLInputType $type, ?HTMLLabelElement $label = null, array $attributes = [])
    {
        try {
            $attributes = HTMLAttributeCollection::createFromArray($attributes);
        } catch(CauseEffectException $e) {
            throw new CauseEffectException('Cannot create ' . self::class, $e->getCause());
        }

        
        parent::__construct($attributes);
        
        $this->label = $label;
        $this->name = $name;
        $this->type = $type->value;

        if($type === HTMLInputType::HIDDEN) return;

        if(!$this->attributes->isset('required')) $this->attributes->add('required', new HTMLAttribute('required'));
    }

    public function getLabel() : ?HTMLLabelElement
    {
        return $this->label;
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

    protected function defineTagName() : void
    {
        $this->tagName = 'input';
    }
}
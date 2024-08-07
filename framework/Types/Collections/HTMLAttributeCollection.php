<?php

declare(strict_types=1);

namespace Framework\Types\Collections;

use Framework\Exceptions\CauseEffectException;
use Framework\Types\Strings\HTMLAttribute;

class HTMLAttributeCollection extends AssociativeCollection
{
    public function __construct(array $items = [])
    {
        $this->items = array_map(function($htmlAttribute) {
            if($htmlAttribute instanceof HTMLAttribute) return $htmlAttribute;
            
            throw new CauseEffectException('Cannot create HTMLAttributeCollection', 'An HTMLAttributeCollection can only contain instances of HTMLAttribute.');
        }, $items);

        parent::__construct($this->items);
    }

    public static function createFromArray(array $attributes) : self
    {
        $items = [];
        
        foreach($attributes as $key => $value)
        {
            if(!is_string($key)) throw new CauseEffectException('Cannot create HTMLAttributeCollection from Array', 'Argument $attributes must be an associative array.');
            if(!is_string($value)) throw new CauseEffectException('Cannot create HTMLAttributeCollection from Array', 'Argument $attributes must only contain values of type string. Received ' . gettype($value) . '.');

            $attribute = new HTMLAttribute($key, $value);
            $items[$key] = $attribute;
        }

        return new self($items);
    }
    
    public function get(string $key): HTMLAttribute
    {
        return parent::get($key);
    }

    public function __toString()
    {
        return implode(' ', $this->items);
    }

    public function current() : HTMLAttribute
    {
        return parent::current();
    }
}
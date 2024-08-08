<?php

declare(strict_types=1);

namespace Framework\Types\Composed\HTMLElements;

use Framework\Exceptions\CauseEffectException;
use Framework\Types\Collections\HTMLAttributeCollection;
use Framework\Types\Strings\HTMLAttribute;
use Stringable;

abstract class HTMLElement implements Stringable
{
    protected ?HTMLAttributeCollection $attributes;
    protected string $tagName = '';

    public function __construct(?HTMLAttributeCollection $attributes = null)
    {
        $this->attributes = (is_null($attributes)) ? new HTMLAttributeCollection() : $attributes;
        $this->defineTagName();
    }

    public function getAttribute(string $name) : string
    {
        try {
            $attribute = $this->attributes->get($name);
        } catch(CauseEffectException $e) {
            throw new CauseEffectException('Cannot get attribute "' . $name . '"', $e->getCause());
        }
        return $attribute->getValue();
    }

    public function setAttribute(string $name, string $value) : self
    {
        $this->attributes->set($name, new HTMLAttribute($name, $value));
 
        return $this;
    }    

    public function getAttributes() : HTMLAttributeCollection
    {
        return $this->attributes;
    }

    public function removeAttribute(string $name) : self
    {
        $this->attributes->remove($name);

        return $this;
    }

    abstract protected function defineTagName() : void;
}
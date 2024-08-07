<?php

declare(strict_types=1);

namespace App\HTMLElements;

use App\Collections\HTMLAttributeCollection;
use App\Collections\HTMLElementCollection;
use App\Exceptions\CauseEffectException;
use App\Types\HTMLAttribute;

class HTMLElement
{
    protected ?HTMLAttributeCollection $attributes;
    protected ?HTMLElementCollection $childs;

    public function __construct(?HTMLAttributeCollection $attributes = null)
    {
        $this->childs = new HTMLElementCollection();
        $this->attributes = (is_null($attributes)) ? new HTMLAttributeCollection() : $attributes;
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

    public function addChild(HTMLElement $htmlElement) : self
    {
        $this->childs->add($htmlElement);

        return $this;
    }

    public function getChilds() : HTMLElementCollection
    {
        return $this->childs;
    }

    public function removeChild(int $index) : void
    {
        $this->childs->remove($index);
    }
}
<?php

namespace Framework\Types\Composed\HTMLElements;

use Framework\Exceptions\CauseEffectException;
use Framework\Types\Collections\HTMLAttributeCollection;
use Framework\Types\Collections\HTMLElementCollection;

abstract class NonVoidElement extends HTMLElement
{
    protected string $innerText = '';
    protected ?HTMLElementCollection $childs;

    public function __construct(?HTMLAttributeCollection $attributes = null)
    {
        parent::__construct($attributes);
        $this->childs = new HTMLElementCollection();
    }

    public function addChild(HTMLElement $htmlElement) : self
    {
        if(!empty($this->innerText)) throw new CauseEffectException('Cannot add child to ' . self::class, 'You cannot add childs if $this->innerText is already defined');
        
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

    public function getOpeningTag() : string
    {
        return '<' . $this->tagName . ' ' . implode(' ', $this->attributes->toArray()) . '>';
    }

    public function getClosingTag() : string
    {
        return '</' . $this->tagName . '>';
    }
    
    public function __toString(): string
    {
        $content = (empty($this->innerText)) ? implode('', $this->childs->toArray()) : $this->innerText;
        return $this->getOpeningTag() . $content . $this->getClosingTag();
    }

    abstract protected function defineTagName() : void;

    public function getInnerText() : string
    {
        return $this->innerText;
    }

    public function setInnerText(string $innerText) : void
    {
        if(!empty($this->attributes->toArray())) throw new CauseEffectException('Cannot add innerText to ' . self::class, 'You cannot set innerText if $this->childs is already defined');

        $this->innerText = $innerText;
    }
}
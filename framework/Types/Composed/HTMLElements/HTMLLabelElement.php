<?php

namespace Framework\Types\Composed\HTMLElements;

use Framework\Exceptions\CauseEffectException;
use Framework\Types\Collections\HTMLAttributeCollection;

class HTMLLabelElement extends NonVoidElement
{
    public function __construct(string $innerText = '', ?array $attributes = [])
    {
        try {
            $attributes = HTMLAttributeCollection::createFromArray($attributes);
        } catch(CauseEffectException $e) {
            throw new CauseEffectException('Cannot create ' . self::class, $e->getCause());
        }
        
        parent::__construct($attributes);
        
        $this->innerText = $innerText;
    }

    protected function defineTagName() : void
    {
        $this->tagName = 'label';
    }
}
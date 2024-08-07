<?php

namespace App\Collections;

use App\HTMLElements\HTMLElement;
use Exception;

class HTMLElementCollection extends IndexedCollection
{
    public function __construct(array $items = [])
    {
        $this->items = array_map(function($htmlElement) {
            if($htmlElement instanceof HTMLElement) return $htmlElement;
            
            throw new Exception('An HTMLElementCollection can only contain instances of HTMLElement.');
        }, $items);

        parent::__construct($this->items);
    }

    public function get(int $index) : HTMLElement
    {
        return parent::get($index);
    }

    public function current() : HTMLElement
    {
        return parent::current();
    }
}
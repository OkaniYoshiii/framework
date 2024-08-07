<?php

namespace Framework\Types\Collections;

use Iterator;

abstract class Collection implements Iterator
{
    protected array $items;
    private int $position = 0;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function current() : object 
    {
        return $this->items[$this->position];
    }

    public function key() : int
    {
        return $this->position;
    }

    public function next() : void
    {
        ++$this->position;
    }

    public function rewind() : void
    {
        $this->position = 0;
    }

    public function valid() : bool
    {
        return isset($this->items[$this->position]);
    }
}
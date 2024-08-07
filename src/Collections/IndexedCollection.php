<?php

namespace App\Collections;

use OutOfBoundsException;

abstract class IndexedCollection extends Collection
{
    public function add(object $item) : void
    {
        $this->items[] = $item;
    }

    public function isset(int $index) : bool
    {
        return isset($this->items[$index]);
    }

    public function get(int $index) : object
    {
        if(!isset($this->items[$index])) throw new OutOfBoundsException('Cannot get item in Collection : Index #' . $index . ' does not exists in Collection');
        
        return $this->items[$index];
    }

    public function set(int $index, object $item) : void
    {
        $this->items[$index] = $item;
    }

    public function remove(int $index) : void
    {
        if(!isset($this->items[$index])) throw new OutOfBoundsException('Cannot remove item to Collection : Index #' . $index . ' does not exists in Collection');

        unset($this->items[$index]);
    }
}
<?php

declare(strict_types=1);

namespace App\Collections;

use App\Exceptions\CauseEffectException;

abstract class AssociativeCollection extends Collection
{
    public function add(string $key, object $item) : void
    {
        $this->items[$key] = $item;
    }

    public function isset(string $key) : bool
    {
        return isset($this->items[$key]);
    }

    public function get(string $key) : object
    {
        if(!isset($this->items[$key])) throw new CauseEffectException('Cannot get item in ' . self::class, 'Key "' . $key . '" does not exists in ' . get_called_class() . '.');

        return $this->items[$key];
    }

    public function set(string $key, object $item) : void
    {
        $this->items[$key] = $item;
    }

    public function remove(string $key) : void
    {
        if(!isset($this->items[$key])) throw new CauseEffectException('Cannot remove item to ' . self::class, 'Key "' . $key . '" does not exists.');

        unset($this->items[$key]);
    }
}
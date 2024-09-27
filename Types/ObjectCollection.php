<?php

namespace Framework\Types;

use Exception;

class ObjectCollection
{
    public function __construct(private readonly string $fqcn, private array $items = [])
    {
        if(!class_exists($fqcn)) throw new Exception('Class ' . $fqcn . ' does not exists');
        if(!$this->isContainingOnlyInstanceOf($fqcn, $items)) throw new Exception('Array "[' . implode(', ', $items) . ']" does not contains only instances of ' . $fqcn);
    }

    private function isContainingOnlyInstanceOf(string $fqcn, array $items) : bool
    {
        $matchedTypes = array_filter($items, fn(mixed $item) => $item instanceof $fqcn);
        return count($matchedTypes) === count($items);
    }

    public function addItem(object $object) : void
    {
        if(!($object instanceof $this->fqcn)) throw new Exception('');
        
        $this->items[] = $object;
    }

    public function getItems() : array
    {
        return $this->items;
    }
}
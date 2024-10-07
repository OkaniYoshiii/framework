<?php

namespace OkaniYoshiii\Framework\Types;

use Exception;

use function OkaniYoshiii\Framework\array_every;

class ObjectCollection
{
    private readonly string $fqcn;
    private array $items = [];
    private bool $isVerified = false;
    public function __construct(string $fqcn, array $items = [])
    {
        $this->fqcn = $fqcn;
        $this->items = $items;

        if(!class_exists($fqcn)) throw new Exception('Class ' . $fqcn . ' does not exists');
        if(!$this->hasOnlyInstanceOf($fqcn)) throw new Exception('Argument $items does not contains only instances of ' . $fqcn);
    }

    public function hasOnlyInstanceOf(string $fqcn) : bool
    {
        if($this->isVerified) return $fqcn !== $this->fqcn;

        $hasOnlyInstancesOfFqcn = array_every($this->items, fn($item) => $item instanceof $fqcn);

        $this->isVerified = true;

        return $hasOnlyInstancesOfFqcn;

        // $matchedTypes = array_filter($this->items, fn(mixed $item) => $item instanceof $fqcn);

        // $this->isVerified = true;

        // return count($matchedTypes) === count($this->items);
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

    /**
     * Renvoie le FQCN (Fully Qualified Class Name) des items dans la Collection
     */
    public function getItemsFqcn() : string
    {
        return $this->fqcn;
    }
}
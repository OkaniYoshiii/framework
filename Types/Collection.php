<?php

namespace Framework\Types;

use Exception;
use Framework\Enums\DataType;

class Collection
{
    public function __construct(private readonly DataType $type, private readonly array $data)
    {
        $this->containsOnlyType($type);
    }

    public function containsOnlyType(DataType $type, bool $throwsException = true) : bool
    {
        $matchedTypes = array_filter($this->data, fn(mixed $item) => ($type::tryFrom(gettype($item)) !== null) ? true : false);
        $containsOnlySpecifiedType = count($matchedTypes) === count($this->data);

        if($throwsException && !$containsOnlySpecifiedType) throw new Exception('Array "[' . implode(', ', $this->data) . ']" does not contains only type of ' . $type->value);
        return $containsOnlySpecifiedType;
    }

    public function containsOnlyInstancesOf(string $fqcn, bool $throwsException = true) : bool
    {
        if($throwsException && !class_exists($fqcn)) throw new Exception('FQCN "' . $fqcn . '" does not exists.');
        if(!$throwsException && !class_exists($fqcn)) return false;

        $matchedTypes = array_filter($this->data, fn(mixed $item) => $item instanceof $fqcn);
        $containsOnlySpecifiedType = count($matchedTypes) === count($this->data);

        if($throwsException && !$containsOnlySpecifiedType) throw new Exception('Array "[' . implode(', ', $this->data) . ']" does not contains only type of ' . $this->type->value);
        return $containsOnlySpecifiedType;
    }

    public function getData() : array
    {
        return $this->data;
    }

    public function getType() : DataType
    {
        return $this->type;
    }
}
<?php

namespace OkaniYoshiii\Framework\Exceptions;

use Exception;
use OkaniYoshiii\Framework\Types\ObjectCollection;

class IncorrectObjectCollectionType extends Exception
{
    public function __construct(ObjectCollection $objectCollection, string $expectedFqcn)
    {
        $message = 'ObjectCollection must contain only instances of '  . $expectedFqcn . '. Received : ' . $objectCollection->getItemsFqcn();
        
        parent::__construct($message);
    }
}
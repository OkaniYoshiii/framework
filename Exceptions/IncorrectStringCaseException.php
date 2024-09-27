<?php

declare(strict_types=1);

namespace Framework\Exceptions;

use Exception;
use Framework\Enums\StringCase;
use Framework\Types\ObjectCollection;

class IncorrectStringCaseException extends Exception
{
    protected $message;
    protected string $consequence;
    protected string $cause;

    public function __construct(string $string, array $expectedCases)
    {
        // Vérifier que l'array en contiennent que des instances de StringCase
        $collection = new ObjectCollection(StringCase::class, $expectedCases);

        $expectedCases = array_map(fn(StringCase $case) => $case->value, $collection->getItems());
        $this->message = 'Incorrect string format : "' . $string . '" is not ' . implode(' or ', $expectedCases);

        parent::__construct($this->message);
    }
}
<?php

namespace OkaniYoshiii\Framework\ORM;

use OkaniYoshiii\Framework\Types\Primitive\SnakeCaseWord;

class SelectStatement extends SQLStatement
{
    private array $fields = [];
    private SnakeCaseWord $from;
    public function __construct(SnakeCaseWord $table, SnakeCaseWord ...$fields)
    {
        foreach($fields as $field)
        {
            $this->fields[] = $table . '.' . $field;
        }
    }

    public function addSelect(SnakeCaseWord $table, SnakeCaseWord ...$fields) : self
    {
        foreach($fields as $field)
        {
            $this->fields[] = $table . '.' . $field;
        }

        return $this;
    }

    public function from(SnakeCaseWord $table) : void
    {
        $this->from = $table;
    } 

    public function validate(): void
    {
        
    }
}
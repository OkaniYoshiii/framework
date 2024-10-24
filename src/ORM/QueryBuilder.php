<?php

namespace OkaniYoshiii\Framework\ORM;

use Exception;
use OkaniYoshiii\Framework\Types\Primitive\SnakeCaseWord;

class QueryBuilder
{
    private array $selectedFields;
    private SQLStatement $statement;
    private SelectStatement $select;
    private SnakeCaseWord $from;

    public function select(SnakeCaseWord $table, SnakeCaseWord ...$fields) : SelectStatement
    {
        $this->statement = new SelectStatement($table, ...$fields);

        return $this->select;
    }

    public function getQuery() : string
    {
        $this->validateQuery();

        return $statement->__toString();
    }

    private function validateQuery() : void
    {
        // SELECT STATEMENTS
        if(isset($this->selectedFields) && !empty($this->selectedFields) && !isset($this->from) ) {
            throw new Exception('SQL Query cannot have a "SELECT" statement if no "FROM" clause has been defined');
        }

        if(isset($this->from) && (!isset($this->selectedFields) || empty($this->selectedFields)))  {
            throw new Exception('SQL Query cannot have a "FROM" clause if no "SELECT" statement has been defined');
        }
    }
}
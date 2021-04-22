<?php

namespace builders\query_builder;

class Where
{
    private string $column;
    private int $comparison;
    private mixed $value;

    public function __construct(string $column, int $comparison, mixed $value)
    {
        $this->column = $column;
        $this->comparison = $comparison;
        $this->value = $value;
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getComparison(): int
    {
        return $this->comparison;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
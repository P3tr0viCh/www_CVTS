<?php

namespace QueryBuilder;

class Where
{
    /**
     * @var string
     */
    private $column;
    /**
     * @var int
     */
    private $comparison;
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param string $column
     * @param int $comparison
     * @param mixed $value
     */
    public function __construct($column, $comparison, $value)
    {
        $this->column = $column;
        $this->comparison = $comparison;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @return int
     */
    public function getComparison()
    {
        return $this->comparison;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
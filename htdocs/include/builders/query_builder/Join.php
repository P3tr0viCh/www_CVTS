<?php

namespace QueryBuilder;

class Join
{
    /**
     * @var string
     */
    private $table;

    /**
     * @var string[]
     */
    private $columns;

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return string[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Join constructor.
     * @param string $table
     * @param string[] $columns
     */
    public function __construct($table, $columns)
    {
        $this->table = $table;
        $this->columns = $columns;
    }
}
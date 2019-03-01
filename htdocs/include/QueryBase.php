<?php

use QueryBuilder\Builder;

require_once "builders/query_builder/Builder.php";

abstract class QueryBase
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * QueryBase constructor.
     */
    public function __construct()
    {
        $this->builder = Builder::getInstance();
    }

    protected abstract function makeQuery();

    public function getQuery()
    {
        $this->builder->clear();

        $this->makeQuery();

        return $this->builder->build();
    }
}
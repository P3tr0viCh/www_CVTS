<?php
require_once "builders/query_builder/Builder.php";

abstract class QueryBase
{
    /**
     * @var \QueryBuilder\Builder
     */
    protected $builder;

    /**
     * QueryBase constructor.
     */
    public function __construct()
    {
        $this->builder = \QueryBuilder\Builder::getInstance();
    }

    protected abstract function makeQuery();

    public function getQuery()
    {
        $this->builder->clear();

        $this->makeQuery();

        return $this->builder->build();
    }
}
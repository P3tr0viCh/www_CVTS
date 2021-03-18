<?php

require_once "builders/query_builder/Builder.php";

use QueryBuilder\Builder;

abstract class QueryBase
{
    protected Builder $builder;

    public function __construct()
    {
        $this->builder = Builder::getInstance();
    }

    protected abstract function makeQuery();

    public function getQuery(): ?string
    {
        $this->builder->clear();

        $this->makeQuery();

        return $this->builder->build();
    }
}
<?php
require_once "builders/query_builder/Builder.php";
require_once "QueryBase.php";

class QueryDateTime extends QueryBase
{
    protected function makeQuery()
    {
        $this->builder->column(database\Columns::DATETIME_NOW);
    }
}
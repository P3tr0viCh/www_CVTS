<?php
require_once "builders/query_builder/Builder.php";
require_once "Database.php";
require_once "QueryBase.php";

class QueryDateTime extends QueryBase
{
    protected function makeQuery()
    {
        $this->builder->column(Database\Columns::DATETIME_NOW);
    }
}
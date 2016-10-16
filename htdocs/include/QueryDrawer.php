<?php
require_once "builders/query_builder/Builder.php";
require_once "Database.php";
require_once "QueryBase.php";

use Database\Tables as T;
use Database\Columns as C;

class QueryDrawer extends QueryBase
{
    protected function makeQuery()
    {
        $this->builder
            ->column(C::SCALE_NUM)
            ->column(C::SCALE_PLACE)
            ->table(T::SCALES)
            ->order(C::SCALE_PLACE, false, 'latin1_bin');
    }
}
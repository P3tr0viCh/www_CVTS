<?php
require_once "builders/query_builder/Builder.php";
require_once "QueryBase.php";

use database\Info as I;
use database\Tables as T;
use database\Columns as C;

class QueryDrawer extends QueryBase
{
    protected function makeQuery()
    {
        $this->builder
            ->column(C::SCALE_NUM)
            ->column(C::SCALE_PLACE)
            ->column(C::SCALE_DISABLED)
            ->table(T::SCALES)
            ->join(T::SCALES_ADD, C::SCALE_NUM)
            ->order(C::SCALE_PLACE, false, I::COLLATE_LATIN);
    }
}
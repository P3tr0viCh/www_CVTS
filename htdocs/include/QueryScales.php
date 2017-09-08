<?php
require_once "builders/query_builder/Builder.php";
require_once "Database.php";
require_once "QueryBase.php";

use Database\Tables as T;
use Database\Columns as C;

class QueryScales extends QueryBase
{
    protected function makeQuery()
    {
        $this->builder
            ->column(C::SCALE_NUM)
            ->column(C::SCALE_TYPE_NAME)
            ->column(C::SCALE_CLASS_STATIC)
            ->column(C::SCALE_CLASS_DYNAMIC)
            ->column(C::SCALE_PLACE)
            ->column(C::SCALE_DISABLED)
            ->table(T::SCALES)
            ->join(T::SCALES_ADD, C::SCALE_NUM)
            ->order(C::SCALE_PLACE, false, 'latin1_bin');
    }
}
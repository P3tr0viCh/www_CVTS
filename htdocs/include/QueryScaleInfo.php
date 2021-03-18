<?php
require_once "builders/query_builder/Builder.php";
require_once "QueryBase.php";

use QueryBuilder\Builder as B;
use database\Tables as T;
use database\Columns as C;

class QueryScaleInfo extends QueryBase
{
    private int $scaleNum;

    public function setScaleNum(int $scaleNum): static
    {
        $this->scaleNum = $scaleNum;
        return $this;
    }

    protected function makeQuery()
    {
        $this->builder
            ->column(C::SCALE_PLACE)
            ->column(C::SCALE_TYPE)
            ->column(C::SCALE_TYPE_DYN)
            ->table(T::SCALES)
            ->join(T::SCALES_ADD, C::SCALE_NUM)
            ->where(C::SCALE_NUM, B::COMPARISON_EQUAL, $this->scaleNum);
    }
}
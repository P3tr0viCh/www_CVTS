<?php
require_once "builders/query_builder/Builder.php";
require_once "QueryBase.php";

use database\Info as I;
use database\Tables as T;
use database\Columns as C;

class QueryScales extends QueryBase
{
    protected static function getScaleClass($typeDyn): string
    {
        return 'IF(' . C::SCALE_TYPE_DYN . '=' . ($typeDyn ? '1' : '0') . ', ' .
            T::LST_WCLASS . '.' . C::TEXT . ', NULL)';
    }

    protected function makeQuery()
    {
        $this->builder
            ->column(C::SCALE_NUM)
            ->column(C::SCALE_TYPE_TEXT)
            ->column(self::getScaleClass(false), NULL, C::SCALE_CLASS_STATIC)
            ->column(self::getScaleClass(true), NULL, C::SCALE_CLASS_DYNAMIC)
            ->column(C::SCALE_PLACE)
            ->column(C::SCALE_MIN_CAPACITY)
            ->column(C::SCALE_MAX_CAPACITY)
            ->column(C::SCALE_DISCRETENESS)
            ->column(C::SCALE_DISABLED)
            ->table(T::SCALES)
            ->join(T::SCALES_ADD, C::SCALE_NUM)
            ->join(T::LST_WCLASS, C::SCALE_WCLASS)
            ->order(C::SCALE_PLACE, false, I::COLLATE_LATIN);
    }
}
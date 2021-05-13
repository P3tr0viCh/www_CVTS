<?php
require_once "builders/query_builder/Builder.php";
require_once "QueryBase.php";

use builders\query_builder\Comparison;
use database\Info as I;
use database\Tables as T;
use database\Columns as C;

class QueryScales extends QueryBase
{
    private bool $showDisabled = false;
    private bool $showAllOperators = false;

    protected static function getScaleClass($typeDyn): string
    {
        return 'IF(' . C::SCALE_TYPE_DYN . '=' . ($typeDyn ? '1' : '0') . ', ' .
            T::LST_WCLASS . '.' . C::TEXT . ', NULL)';
    }

    public function setShowDisabled(bool $showDisabled): static
    {
        $this->showDisabled = $showDisabled;
        return $this;
    }

    public function setShowAllOperators(bool $showAllOperators): static
    {
        $this->showAllOperators = $showAllOperators;
        return $this;
    }

    private function setTable()
    {
        $this->builder->table(T::SCALES);
    }

    private function setColumns()
    {
        $this->builder
            ->column(C::SCALE_NUM)
            ->column(C::SCALE_TYPE_TEXT)
            ->column(self::getScaleClass(false), NULL, C::SCALE_CLASS_STATIC)
            ->column(self::getScaleClass(true), NULL, C::SCALE_CLASS_DYNAMIC)
            ->column(C::SCALE_PLACE)
            ->column(C::SCALE_MIN_CAPACITY)
            ->column(C::SCALE_MAX_CAPACITY)
            ->column(C::SCALE_DISCRETENESS);
    }

    private function setJoin()
    {
        $this->builder
            ->join(T::SCALES_ADD, C::SCALE_NUM)
            ->join(T::LST_WCLASS, C::SCALE_WCLASS);

        if (!$this->showAllOperators) {
            $this->builder->join(T::LST_OPERATOR, C::SCALE_OPERATOR);
        }
    }

    private function setWhere()
    {
        if (!$this->showDisabled) {
            $this->builder->where(C::SCALE_DISABLED, Comparison::EQUAL, false);
        }

        if (!$this->showAllOperators) {
            $this->builder->where(C::SCALE_OPERATOR, Comparison::EQUAL, Constants::MY_OPERATOR_ID);
        }
    }

    private function setOrder()
    {
        $this->builder->order(C::SCALE_PLACE, false, I::COLLATE_LATIN);
    }

    protected function makeQuery()
    {
        $this->setTable();
        $this->setColumns();
        $this->setJoin();
        $this->setWhere();
        $this->setOrder();
    }
}
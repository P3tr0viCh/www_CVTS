<?php
require_once "builders/query_builder/Builder.php";
require_once "QueryBaseDates.php";

use JetBrains\PhpStorm\Pure;
use builders\query_builder\Comparison;
use database\Info as I;
use database\Tables as T;
use database\Columns as C;

class QueryCoeffs extends QueryBaseDates
{
    private int $scaleNum = Constants::SCALE_NUM_ALL_TRAIN_SCALES;

    private bool $showDisabled = false;

    public function setScaleNum(int $scaleNum): static
    {
        $this->scaleNum = $scaleNum;
        return $this;
    }

    public function setShowDisabled(int $showDisabled): static
    {
        $this->showDisabled = $showDisabled;
        return $this;
    }

    #[Pure] private function isAllScales(): bool
    {
        return $this->scaleNum == Constants::SCALE_NUM_ALL_TRAIN_SCALES;
    }

    #[Pure] private function getTableName(): string
    {
        return T::COEFFS;
    }

    private function setColumns()
    {
        if ($this->isAllScales()) {
            $this->builder->column(C::SCALE_NUM);
            $this->builder->column(C::SCALE_PLACE);
        }

        $this->builder
            ->column(C::DATETIME_END)
            ->column(C::COEFFICIENT_P1)
            ->column(C::COEFFICIENT_Q1)
            ->column(C::TEMPERATURE_1, null, C::COEFFICIENT_T1)
            ->column(C::COEFFICIENT_P2)
            ->column(C::COEFFICIENT_Q2)
            ->column(C::TEMPERATURE_2, null, C::COEFFICIENT_T2);
    }

    private function setOrder()
    {
        if ($this->isAllScales()) {
            $this->builder->order(C::SCALE_PLACE, false, I::COLLATE_LATIN);
        }

        $this->builder->order(C::DATETIME_END, true);
    }

    private function setWhere()
    {
        if (!$this->isAllScales()) {
            $this->builder->where(C::SCALE_NUM, Comparison::EQUAL, $this->scaleNum);
        }

        $this->builder
            ->where(C::DATETIME_END, Comparison::GREATER_OR_EQUAL, $this->getDateTimeStart())
            ->where(C::DATETIME_END, Comparison::LESS_OR_EQUAL, $this->getDateTimeEnd());

        if ($this->isAllScales()) {
            $this->builder->where(C::SCALE_TYPE_DYN, Comparison::EQUAL, true);

            if (!$this->showDisabled) {
                $this->builder->where(C::SCALE_DISABLED, Comparison::EQUAL, false);
            }
        }
    }

    private function setJoin()
    {
        if ($this->isAllScales()) {
            $this->builder->join(T::SCALES, C::SCALE_NUM);
            $this->builder->join(T::SCALES_ADD, C::SCALE_NUM);
        }
    }

    protected function makeQuery()
    {
        $this->builder->table($this->getTableName());

        $this->setColumns();

        $this->setJoin();

        $this->setWhere();

        $this->setOrder();
    }
}
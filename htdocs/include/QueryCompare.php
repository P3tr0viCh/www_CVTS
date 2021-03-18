<?php
require_once "builders/query_builder/Builder.php";
require_once "QueryBase.php";

use QueryBuilder\Builder as B;
use database\Tables as T;
use database\Columns as C;

class QueryCompare extends QueryBase
{
    /**
     * Сравнение массы.
     * Глубина поиска в секундах.
     * 2678400 == 31 день в секундах.
     */
    const COMPARE_PERIOD = 2678400;

    private int $scaleNum;
    private string $vanNumber;
    private bool $compareByBrutto;
    private bool $compareForward;
    private int $dateTime;

    public function setScaleNum(int $scaleNum): static
    {
        $this->scaleNum = $scaleNum;
        return $this;
    }

    public function setVanNumber(string $vanNumber): static
    {
        $this->vanNumber = $vanNumber;
        return $this;
    }

    public function setCompareByBrutto(bool $compareByBrutto): static
    {
        $this->compareByBrutto = $compareByBrutto;
        return $this;
    }

    public function setCompareForward(bool $compareForward): static
    {
        $this->compareForward = $compareForward;
        return $this;
    }

    public function setDateTime(int $dateTime): static
    {
        $this->dateTime = $dateTime;
        return $this;
    }

    protected function makeQuery()
    {
        $this->builder
            ->column(C::SCALE_NUM)
            ->column($this->compareByBrutto ?
                C::BRUTTO :
                C::NETTO)
            ->column(C::DATETIME)
            ->table(T::VAN_DYNAMIC_AND_STATIC_BRUTTO)
            ->where(C::SCALE_NUM, B::COMPARISON_NOT_EQUAL, $this->scaleNum)
            ->where(C::VAN_NUMBER, B::COMPARISON_EQUAL, $this->vanNumber)
            ->order(C::UNIX_TIME, true)
            ->limit(1);

        if ($this->compareForward) {
            $dateTimeStart = $this->dateTime;
            $dateTimeEnd = $dateTimeStart + self::COMPARE_PERIOD;

            $this->builder->where(C::UNIX_TIME, B::COMPARISON_GREATER, $dateTimeStart);
            $this->builder->where(C::UNIX_TIME, B::COMPARISON_LESS_OR_EQUAL, $dateTimeEnd);
        } else {
            $dateTimeEnd = $this->dateTime;
            $dateTimeStart = $dateTimeEnd - self::COMPARE_PERIOD;

            $this->builder->where(C::UNIX_TIME, B::COMPARISON_GREATER_OR_EQUAL, $dateTimeStart);
            $this->builder->where(C::UNIX_TIME, B::COMPARISON_LESS, $dateTimeEnd);
        }
    }
}
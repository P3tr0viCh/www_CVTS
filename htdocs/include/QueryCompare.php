<?php
require_once "builders/query_builder/Builder.php";
require_once "QueryBase.php";

use QueryBuilder\Expr as E;
use QueryBuilder\Builder as B;
use database\Tables as T;
use database\Columns as C;
use database\Aliases as A;

class QueryCompare extends QueryBase
{
    const UNION = '%s UNION %s';
    const SUB_QUERY = '(%s) %s %s';

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

    /**
     * @throws Exception
     */
    protected function makeQuery()
    {
        if ($this->compareForward) {
            $dateTimeStart = $this->dateTime;
            $dateTimeEnd = date_add(date_create()->setTimestamp($this->dateTime),
                new DateInterval('P' . TimePeriods::COMPARE . 'D'))->getTimestamp();
        } else {
            $dateTimeEnd = $this->dateTime;
            $dateTimeStart = date_sub(date_create()->setTimestamp($this->dateTime),
                new DateInterval('P' . TimePeriods::COMPARE . 'D'))->getTimestamp();
        }
        $dateTimeStart = (float)date(self::MYSQL_DATETIME_FORMAT, $dateTimeStart);
        $dateTimeEnd = (float)date(self::MYSQL_DATETIME_FORMAT, $dateTimeEnd);

        $builder = B::getInstance();

        $builder
            ->column(C::SCALE_NUM)
            ->column($this->compareByBrutto ?
                C::BRUTTO :
                C::NETTO)
            ->column(C::DATETIME)
            ->where(C::SCALE_NUM, B::COMPARISON_NOT_EQUAL, $this->scaleNum)
            ->where(C::DATETIME, B::COMPARISON_GREATER, $dateTimeStart)
            ->where(C::DATETIME, B::COMPARISON_LESS, $dateTimeEnd)
            ->where(C::VAN_NUMBER, B::COMPARISON_EQUAL, $this->vanNumber);

        $builderDyn = clone $builder;
        $builderSta = clone $builder;
        $builderDyn->table(T::VAN_DYNAMIC_BRUTTO);
        $builderSta->table(T::VAN_STATIC_BRUTTO);

        $this->builder
            ->table(sprintf(self::SUB_QUERY,
                sprintf(self::UNION, $builderDyn->build(), $builderSta->build()), E::EXPR_AS, A::NU))
            ->order(C::DATETIME, true) // Возможно, здесь должна быть зависимость от $compareForward
            ->limit(1);
    }
}
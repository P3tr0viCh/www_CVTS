<?php
require_once "builders/query_builder/Builder.php";
require_once "Database.php";
require_once "QueryBase.php";

use QueryBuilder\Builder as B;
use Database\Tables as T;
use Database\Columns as C;

class QueryCompare extends QueryBase
{
    /**
     * Сравнение массы.
     * Глубина поиска в секундах.
     * 2678394 == 31 день в секундах без 6 секунд.
     * Не помню, почему без 6 секунд.
     */
    const COMPARE_PERIOD = 2678394;

    /**
     * @var int
     */
    private $scaleNum;
    /**
     * @var string
     */
    private $vanNumber;
    /**
     * @var bool
     */
    private $compareByBrutto;
    /**
     * @var bool
     */
    private $compareForward;
    /**
     * @var int
     */
    private $dateTime;

    /**
     * @param int $scaleNum
     * @return $this
     */
    public function setScaleNum($scaleNum)
    {
        $this->scaleNum = $scaleNum;
        return $this;
    }

    /**
     * @param string $vanNumber
     * @return $this
     */
    public function setVanNumber($vanNumber)
    {
        $this->vanNumber = $vanNumber;
        return $this;
    }

    /**
     * @param boolean $compareByBrutto
     * @return $this
     */
    public function setCompareByBrutto($compareByBrutto)
    {
        $this->compareByBrutto = $compareByBrutto;
        return $this;
    }

    /**
     * @param boolean $compareForward
     * @return $this
     */
    public function setCompareForward($compareForward)
    {
        $this->compareForward = $compareForward;
        return $this;
    }

    /**
     * @param int $dateTime
     * @return $this
     */
    public function setDateTime($dateTime)
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
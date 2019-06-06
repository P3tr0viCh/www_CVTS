<?php
require_once "builders/query_builder/Builder.php";
require_once "Constants.php";
require_once "Database.php";
require_once "QueryBase.php";

use QueryBuilder\Builder as B;
use Database\Tables as T;
use Database\Columns as C;
use Database\Aliases as A;

class QueryVanListTare extends QueryBase
{
    const ALIAS = '(%s) %s';
    const UNION = '%s UNION %s';

    const MYSQL_DATE_START_FORMAT = "Ymd000000";
    const MYSQL_DATE_END_FORMAT = "Ymd235959";

    private $dateStart;
    private $dateEnd;

    private $vanList;

    /**
     * @param int $dateStart
     * @return QueryVanListTare
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;
        return $this;
    }

    /**
     * @param int $dateEnd
     * @return QueryVanListTare
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    /**
     * @param null|array $vanList
     * @return QueryVanListTare
     */
    public function setVanList($vanList)
    {
        $this->vanList = $vanList;
        return $this;
    }

    /**
     * @throws Exception
     */
    protected function makeQuery()
    {
        $builder = B::getInstance();
        $builder
            ->column(C::VAN_NUMBER)
            ->column(C::DATETIME)
            ->column(C::SCALE_NUM)
            ->column(C::TARE);

        $builderDyn = clone $builder;
        $builderDyn->table(T::VAN_DYNAMIC_TARE);
        $builderSta = clone $builder;
        $builderSta->table(T::VAN_STATIC_TARE);

        $union = sprintf(self::UNION, $builderDyn->build(), $builderSta->build());

        $builderQuery = B::getInstance();
        $builderQuery->table(sprintf(self::ALIAS, $union, A::VANLIST_TARE_UNION));

        if ($this->dateStart) {
            $this->dateStart = (float)date(self::MYSQL_DATE_START_FORMAT,$this->dateStart);
        }
        if ($this->dateEnd) {
            $this->dateEnd = (float)date(self::MYSQL_DATE_END_FORMAT, $this->dateEnd);
        }

        if ($this->dateStart) {
            $builderQuery->where(C::DATETIME, B::COMPARISON_GREATER_OR_EQUAL, $this->dateStart);
        }
        if ($this->dateEnd) {
            $builderQuery->where(C::DATETIME, B::COMPARISON_LESS_OR_EQUAL, $this->dateEnd);
        }

        $builderQuery
            ->where(C::VAN_NUMBER, B::COMPARISON_IN, vanListArrayToString($this->vanList))
            ->order(C::VAN_NUMBER)
            ->order(C::DATETIME, true);

        $this->builder
            ->table(sprintf(self::ALIAS, $builderQuery->build(), A::VANLIST_TARE_QUERY))
            ->group(C::VAN_NUMBER);
    }
}
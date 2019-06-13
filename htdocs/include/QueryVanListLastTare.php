<?php
require_once "builders/query_builder/Builder.php";
require_once "Constants.php";
require_once "Database.php";
require_once "QueryBase.php";

use QueryBuilder\Builder as B;
use Database\Tables as T;
use Database\Columns as C;
use Database\Aliases as A;

class QueryVanListLastTare extends QueryBase
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
     * @return QueryVanListLastTare
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;
        return $this;
    }

    /**
     * @param int $dateEnd
     * @return QueryVanListLastTare
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    /**
     * @param null|array $vanList
     * @return QueryVanListLastTare
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

        if ($this->dateStart) {
            $this->dateStart = (float)date(self::MYSQL_DATE_START_FORMAT, $this->dateStart);
        }
        if ($this->dateEnd) {
            $this->dateEnd = (float)date(self::MYSQL_DATE_END_FORMAT, $this->dateEnd);
        }

        if ($this->dateStart) {
            $builder->where(C::DATETIME, B::COMPARISON_GREATER_OR_EQUAL, $this->dateStart);
        }
        if ($this->dateEnd) {
            $builder->where(C::DATETIME, B::COMPARISON_LESS_OR_EQUAL, $this->dateEnd);
        }

        $vanList = vanListArrayToString($this->vanList);
        if (empty($vanList)) {
            $builder->where(C::VAN_NUMBER, B::COMPARISON_NOT_EQUAL, "");
        } else {
            $builder->where(C::VAN_NUMBER, B::COMPARISON_IN, $vanList);
        }

        $builderDyn = clone $builder;
        $builderDyn->table(T::VAN_DYNAMIC_TARE);
        $builderSta = clone $builder;
        $builderSta->table(T::VAN_STATIC_TARE);

        $union = sprintf(self::UNION, $builderDyn->build(), $builderSta->build());

        $builderQuery = B::getInstance();
        $builderQuery->table(sprintf(self::ALIAS, $union, A::VANLIST_LAST_TARE_UNION));
        $builderQuery
            ->order(C::VAN_NUMBER)
            ->order(C::DATETIME, true);

        $this->builder
            ->table(sprintf(self::ALIAS, $builderQuery->build(), A::VANLIST_LAST_TARE_QUERY))
            ->group(C::VAN_NUMBER);
    }
}
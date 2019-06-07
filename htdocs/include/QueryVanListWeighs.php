<?php
require_once "builders/query_builder/Builder.php";
require_once "Constants.php";
require_once "Database.php";
require_once "QueryBase.php";

use QueryBuilder\Builder as B;
use Database\Tables as T;
use Database\Columns as C;
use Database\Aliases as A;

class QueryVanListWeighs extends QueryBase
{
    const ALIAS = '(%s) %s';
    const UNION = '%s UNION %s';
    const NULL = '%s UNION %s';

    const MYSQL_DATE_START_FORMAT = "Ymd000000";
    const MYSQL_DATE_END_FORMAT = "Ymd235959";

    private $dateStart;
    private $dateEnd;

    private $vanList;

    /**
     * @param int $dateStart
     * @return QueryVanListWeighs
     *
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;
        return $this;
    }

    /**
     * @param int $dateEnd
     * @return QueryVanListWeighs
     *
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    /**
     * @param null|array $vanList
     * @return QueryVanListWeighs
     *
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

        $builder->where(C::VAN_NUMBER, B::COMPARISON_IN, vanListArrayToString($this->vanList));

        $builderB = clone $builder;
        $builderB
            ->column(C::VAN_NUMBER)
            ->column(C::DATETIME)
            ->column(C::SCALE_NUM)
            ->column(C::CARGO_TYPE)
            ->column(C::BRUTTO)
            ->column(C::TARE)
            ->column(C::NETTO);

        $builderDynB = clone $builderB;
        $builderDynB->table(T::VAN_DYNAMIC_BRUTTO);
        $builderStaB = clone $builderB;
        $builderStaB->table(T::VAN_STATIC_BRUTTO);

        $builderT = clone $builder;
        $builderT
            ->column(C::VAN_NUMBER)
            ->column(C::DATETIME)
            ->column(C::SCALE_NUM)
            ->column(C::NULL)
            ->column(C::NULL)
            ->column(C::TARE)
            ->column(C::NULL);

        $builderDynT = clone $builderT;
        $builderDynT->table(T::VAN_DYNAMIC_TARE);
        $builderStaT = clone $builderT;
        $builderStaT->table(T::VAN_STATIC_TARE);

        $union = sprintf(self::UNION, $builderDynB->build(), $builderStaB->build());
        $union = sprintf(self::UNION, $union, $builderDynT->build());
        $union = sprintf(self::UNION, $union, $builderStaT->build());

        $this->builder
            ->table(sprintf(self::ALIAS, $union, A::VANLIST_WEIGHS))
            ->order(C::VAN_NUMBER)
            ->order(C::DATETIME)
            ->order(C::SCALE_NUM);
    }
}
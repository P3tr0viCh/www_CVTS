<?php
require_once "builders/query_builder/Builder.php";
require_once "Constants.php";
require_once "QueryBase.php";

use builders\query_builder\Builder as B;
use builders\query_builder\Comparison;
use database\Tables as T;
use database\Columns as C;
use database\Aliases as A;

class QueryVanListWeighs extends QueryBase
{
    const SUB_QUERY = '(%s)';
    const UNION = '%s UNION %s';

    const MYSQL_DATE_START_FORMAT = "Ymd000000";
    const MYSQL_DATE_END_FORMAT = "Ymd235959";

    private mixed $dateStart;
    private mixed $dateEnd;

    private array $vanList;

    public function setDateStart(?int $dateStart): static
    {
        $this->dateStart = $dateStart;
        return $this;
    }

    public function setDateEnd(?int $dateEnd): static
    {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    public function setVanList(array $vanList): static
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
            $builder->where(C::DATETIME, Comparison::GREATER_OR_EQUAL, $this->dateStart);
        }
        if ($this->dateEnd) {
            $builder->where(C::DATETIME, Comparison::LESS_OR_EQUAL, $this->dateEnd);
        }

        $builder->where(C::VAN_NUMBER, Comparison::IN, vanListArrayToString($this->vanList));

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
            ->table(sprintf(self::SUB_QUERY, $union), A::NU)
            ->order(C::VAN_NUMBER)
            ->order(C::DATETIME)
            ->order(C::SCALE_NUM);
    }
}
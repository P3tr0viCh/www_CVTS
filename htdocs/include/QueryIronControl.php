<?php
require_once "builders/query_builder/Builder.php";
require_once "Constants.php";
require_once "Database.php";
require_once "QueryBase.php";

use QueryBuilder\Builder as B;
use Database\Tables as T;
use Database\Columns as C;
use Database\Aliases as A;

class QueryIronControl extends QueryBase
{
    const SUB_QUERY = '(%s) %s %s';

    const MYSQL_DATETIME_FORMAT = "YmdHis";

    private $dateStart;
    private $dateStartSta;
    private $dateEnd;

    /**
     * @param int $dateStart
     * @return QueryIronControl
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;
        return $this;
    }

    /**
     * @param int $dateEnd
     * @return QueryIronControl
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    /**
     * @throws Exception
     */
    protected function makeQuery()
    {
        $builder = B::getInstance();

        $builderDyn = clone $builder;
        $builderDyn
            ->column(C::VAN_NUMBER)
            ->column(C::SCALE_NUM)
            ->column(C::DATETIME)
            ->column(C::CARRYING)
            ->column(C::NETTO)
            ->column(C::BRUTTO_FIRST_CARRIAGE . ' - ' . C::BRUTTO_SECOND_CARRIAGE, null, C::IRON_CONTROL_DIFF_CARRIAGE)
            ->table(T::VAN_DYNAMIC_BRUTTO)
            ->where(C::SCALE_NUM, B::COMPARISON_IN, ScaleNums::IRON_COMPARE_DYN);

        $builderCargo = clone $builder;
        $builderCargo
            ->column(C::VAN_NUMBER)
            ->table(T::VAN_DYNAMIC_BRUTTO)
            ->where(C::SCALE_NUM, B::COMPARISON_IN, ScaleNums::IRON_COMPARE_DYN);

        $builderSta = clone $builder;
        $builderSta
            ->column(C::VAN_NUMBER)
            ->column(C::SCALE_NUM)
            ->column(C::DATETIME)
            ->column(C::NETTO)
            ->where(C::SCALE_NUM, B::COMPARISON_IN, ScaleNums::IRON_COMPARE_STA)
            ->table(T::VAN_STATIC_BRUTTO);

        if ($this->dateStart) {
            $this->dateStartSta = date_sub((new DateTime())->setTimestamp($this->dateStart),
                new DateInterval('P' . TimePeriods::IRON_COMPARE_FIND . 'D'))->getTimestamp();

            $this->dateStart = (float)date(self::MYSQL_DATETIME_FORMAT, $this->dateStart);
            $this->dateStartSta = (float)date(self::MYSQL_DATETIME_FORMAT, $this->dateStartSta);
        }
        if ($this->dateEnd) {
            $this->dateEnd = (float)date(self::MYSQL_DATETIME_FORMAT, $this->dateEnd);
        }

        if ($this->dateStart) {
            $builderDyn->where(C::DATETIME, B::COMPARISON_GREATER_OR_EQUAL, $this->dateStart);
            $builderCargo->where(C::DATETIME, B::COMPARISON_GREATER_OR_EQUAL, $this->dateStart);

            $builderSta->where(C::DATETIME, B::COMPARISON_GREATER_OR_EQUAL, $this->dateStartSta);
        }
        if ($this->dateEnd) {
            $builderDyn->where(C::DATETIME, B::COMPARISON_LESS_OR_EQUAL, $this->dateEnd);
            $builderSta->where(C::DATETIME, B::COMPARISON_LESS_OR_EQUAL, $this->dateEnd);
            $builderCargo->where(C::DATETIME, B::COMPARISON_LESS_OR_EQUAL, $this->dateEnd);
        }

        $cargoTypeIronDyn = utf8ToLatin1(CargoTypes::IRON_COMPARE_DYN);
        $cargoTypeIronSta = utf8ToLatin1(CargoTypes::IRON_COMPARE_STA);

        $builderDyn->where(C::CARGO_TYPE, B::COMPARISON_EQUAL, $cargoTypeIronDyn);
        $builderCargo->where(C::CARGO_TYPE, B::COMPARISON_EQUAL, $cargoTypeIronDyn);

        $builderSta->where(C::VAN_NUMBER, B::COMPARISON_IN, $builderCargo);
        $builderSta->where(C::CARGO_TYPE, B::COMPARISON_EQUAL, $cargoTypeIronSta);

        $this->builder
            ->column(C::SCALE_NUM, A::IRON_CONTROL_STA, C::IRON_CONTROL_SCALES_STA)
            ->column(C::DATETIME, A::IRON_CONTROL_STA, C::IRON_CONTROL_DATETIME_STA)
            ->column(C::VAN_NUMBER)
            ->column(C::CARRYING)
            ->column(C::NETTO, A::IRON_CONTROL_STA, C::IRON_CONTROL_NETTO_STA)
            ->column(C::NETTO, A::IRON_CONTROL_DYN, C::IRON_CONTROL_NETTO_DYN)
            ->column(A::IRON_CONTROL_DYN . '.' . C::NETTO . ' - ' . C::CARRYING, null, C::IRON_CONTROL_DIFF_DYN_CARR)
            ->column(A::IRON_CONTROL_DYN . '.' . C::NETTO . ' - ' . A::IRON_CONTROL_STA . '.' . C::NETTO, null, C::IRON_CONTROL_DIFF_DYN_STA)
            ->column(C::IRON_CONTROL_DIFF_CARRIAGE)
            ->column(C::DATETIME, A::IRON_CONTROL_DYN, C::IRON_CONTROL_DATETIME_DYN)
            ->column(C::SCALE_NUM, A::IRON_CONTROL_DYN, C::IRON_CONTROL_SCALES_DYN)
            ->table(sprintf(self::SUB_QUERY, $builderDyn->build(), QueryBuilder\Expr::EXPR_AS, A::IRON_CONTROL_DYN))
            ->join(sprintf(self::SUB_QUERY, $builderSta->build(), QueryBuilder\Expr::EXPR_AS, A::IRON_CONTROL_STA), C::VAN_NUMBER)
            ->order(C::IRON_CONTROL_SCALES_DYN)
            ->order(C::IRON_CONTROL_DATETIME_DYN, true);
    }
}
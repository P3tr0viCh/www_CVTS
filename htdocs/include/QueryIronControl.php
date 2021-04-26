<?php
require_once "builders/query_builder/Builder.php";
require_once "Constants.php";
require_once "QueryBase.php";

use builders\query_builder\Builder as B;
use builders\query_builder\Comparison;
use database\Tables as T;
use database\Columns as C;
use database\Aliases as A;

class QueryIronControl extends QueryBase
{
    const SUB_QUERY = '(%s)';

    private mixed $dateStart;
    private mixed $dateStartSta;
    private mixed $dateEnd;

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
            ->column(C::BRUTTO_NEAR_SIDE . ' - ' . C::BRUTTO_FAR_SIDE, null, C::IRON_CONTROL_DIFF_SIDE)
            ->column(C::BRUTTO_FIRST_CARRIAGE . ' - ' . C::BRUTTO_SECOND_CARRIAGE, null, C::IRON_CONTROL_DIFF_CARRIAGE)
            ->table(T::VAN_DYNAMIC_BRUTTO)
            ->where(C::SCALE_NUM, Comparison::IN, ScaleNums::IRON_COMPARE_DYN);

        $builderCargo = clone $builder;
        $builderCargo
            ->column(C::VAN_NUMBER)
            ->table(T::VAN_DYNAMIC_BRUTTO)
            ->where(C::SCALE_NUM, Comparison::IN, ScaleNums::IRON_COMPARE_DYN);

        $builderSta = clone $builder;
        $builderSta
            ->column(C::VAN_NUMBER)
            ->column(C::SCALE_NUM)
            ->column(C::DATETIME)
            ->column(C::NETTO)
            ->where(C::SCALE_NUM, Comparison::IN, ScaleNums::IRON_COMPARE_STA)
            ->table(T::VAN_STATIC_BRUTTO);

        if ($this->dateStart) {
            $this->dateStartSta = date_sub(date_create()->setTimestamp($this->dateStart),
                new DateInterval('P' . TimePeriods::IRON_COMPARE . 'D'))->getTimestamp();

            $this->dateStart = (float)date(self::MYSQL_DATETIME_FORMAT, $this->dateStart);
            $this->dateStartSta = (float)date(self::MYSQL_DATETIME_FORMAT, $this->dateStartSta);
        }
        if ($this->dateEnd) {
            $this->dateEnd = (float)date(self::MYSQL_DATETIME_FORMAT, $this->dateEnd);
        }

        if ($this->dateStart) {
            $builderDyn->where(C::DATETIME, Comparison::GREATER_OR_EQUAL, $this->dateStart);
            $builderCargo->where(C::DATETIME, Comparison::GREATER_OR_EQUAL, $this->dateStart);

            $builderSta->where(C::DATETIME, Comparison::GREATER_OR_EQUAL, $this->dateStartSta);
        }
        if ($this->dateEnd) {
            $builderDyn->where(C::DATETIME, Comparison::LESS_OR_EQUAL, $this->dateEnd);
            $builderSta->where(C::DATETIME, Comparison::LESS_OR_EQUAL, $this->dateEnd);
            $builderCargo->where(C::DATETIME, Comparison::LESS_OR_EQUAL, $this->dateEnd);
        }

        $cargoTypeIronDyn = utf8ToLatin1(CargoTypes::IRON_COMPARE_DYN);
        $cargoTypeIronSta = utf8ToLatin1(CargoTypes::IRON_COMPARE_STA);

        $builderDyn->where(C::CARGO_TYPE, Comparison::EQUAL, $cargoTypeIronDyn);
        $builderCargo->where(C::CARGO_TYPE, Comparison::EQUAL, $cargoTypeIronDyn);

        $builderSta->where(C::VAN_NUMBER, Comparison::IN, $builderCargo);
        $builderSta->where(C::CARGO_TYPE, Comparison::EQUAL, $cargoTypeIronSta);

        $this->builder
            ->column(C::SCALE_NUM, A::IRON_CONTROL_STA, C::IRON_CONTROL_SCALES_STA)
            ->column(C::DATETIME, A::IRON_CONTROL_STA, C::IRON_CONTROL_DATETIME_STA)
            ->column(C::VAN_NUMBER)
            ->column(C::CARRYING)
            ->column(C::NETTO, A::IRON_CONTROL_STA, C::IRON_CONTROL_NETTO_STA)
            ->column(C::NETTO, A::IRON_CONTROL_DYN, C::IRON_CONTROL_NETTO_DYN)
            ->column(A::IRON_CONTROL_DYN . '.' . C::NETTO . ' - ' . C::CARRYING, null, C::IRON_CONTROL_DIFF_DYN_CARR)
            ->column(A::IRON_CONTROL_DYN . '.' . C::NETTO . ' - ' . A::IRON_CONTROL_STA . '.' . C::NETTO, null, C::IRON_CONTROL_DIFF_DYN_STA)
            ->column(C::IRON_CONTROL_DIFF_SIDE)
            ->column(C::IRON_CONTROL_DIFF_CARRIAGE)
            ->column(C::DATETIME, A::IRON_CONTROL_DYN, C::IRON_CONTROL_DATETIME_DYN)
            ->column(C::SCALE_NUM, A::IRON_CONTROL_DYN, C::IRON_CONTROL_SCALES_DYN)
            ->table(sprintf(self::SUB_QUERY, $builderDyn->build()), A::IRON_CONTROL_DYN)
            ->join(sprintf(self::SUB_QUERY, $builderSta->build()), C::VAN_NUMBER, A::IRON_CONTROL_STA)
            ->order(C::IRON_CONTROL_SCALES_DYN)
            ->order(C::IRON_CONTROL_DATETIME_DYN, true);
    }
}
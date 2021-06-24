<?php
require_once "builders/query_builder/Builder.php";
require_once "Constants.php";
require_once "QueryBase.php";

use builders\query_builder\Builder as B;
use builders\query_builder\Comparison;
use database\Tables as T;
use database\Columns as C;
use database\Aliases as A;

class QueryControl extends QueryBase
{
    const SUB_QUERY = '(%s)';

    private int $resultType = ResultType::IRON_CONTROL;

    private mixed $dateStart;
    private mixed $dateStartSta;
    private mixed $dateEnd;

    public function setResultType(int $resultType): static
    {
        $this->resultType = $resultType;
        return $this;
    }

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

        switch ($this->resultType) {
            case ResultType::IRON_CONTROL:
                $scaleNumsSta = ScaleNums::IRON_COMPARE_STA;
                $scaleNumsDyn = ScaleNums::IRON_COMPARE_DYN;
                $cargoTypeSta = utf8ToLatin1(CargoTypes::IRON_COMPARE_STA);
                $cargoTypeDyn = utf8ToLatin1(CargoTypes::IRON_COMPARE_DYN);
                $timePeriod = TimePeriods::IRON_COMPARE;
                $columnScalesSta = C::IRON_CONTROL_SCALES_STA;
                $columnScalesDyn = C::IRON_CONTROL_SCALES_DYN;
                $columnDateTimeSta = C::IRON_CONTROL_DATETIME_STA;
                $columnDateTimeDyn = C::IRON_CONTROL_DATETIME_DYN;
                $columnNettoSta = C::IRON_CONTROL_NETTO_STA;
                $columnNettoDyn = C::IRON_CONTROL_NETTO_DYN;
                $columnDiffDynCarr = C::IRON_CONTROL_DIFF_DYN_CARR;
                $columnDiffDynSta = C::IRON_CONTROL_DIFF_DYN_STA;
                $columnDiffSide = C::IRON_CONTROL_DIFF_SIDE;
                $columnDiffCarriage = C::IRON_CONTROL_DIFF_CARRIAGE;
                break;
            case ResultType::SLAG_CONTROL:
                $scaleNumsSta = ScaleNums::SLAG_COMPARE_STA;
                $scaleNumsDyn = ScaleNums::SLAG_COMPARE_DYN;
                $cargoTypeSta = utf8ToLatin1(CargoTypes::SLAG_COMPARE_STA);
                $cargoTypeDyn = utf8ToLatin1(CargoTypes::SLAG_COMPARE_DYN);
                $timePeriod = TimePeriods::SLAG_COMPARE;
                $columnScalesSta = C::SLAG_CONTROL_SCALES_STA;
                $columnScalesDyn = C::SLAG_CONTROL_SCALES_DYN;
                $columnDateTimeSta = C::SLAG_CONTROL_DATETIME_STA;
                $columnDateTimeDyn = C::SLAG_CONTROL_DATETIME_DYN;
                $columnNettoSta = C::SLAG_CONTROL_NETTO_STA;
                $columnNettoDyn = C::SLAG_CONTROL_NETTO_DYN;
                $columnDiffDynCarr = C::SLAG_CONTROL_DIFF_DYN_CARR;
                $columnDiffDynSta = C::SLAG_CONTROL_DIFF_DYN_STA;
                $columnDiffSide = C::SLAG_CONTROL_DIFF_SIDE;
                $columnDiffCarriage = C::SLAG_CONTROL_DIFF_CARRIAGE;
                break;
            default:
                throw new InvalidArgumentException("Unknown resultType ($this->resultType)");
        }

        $builderDyn = clone $builder;
        $builderDyn
            ->column(C::VAN_NUMBER)
            ->column(C::SCALE_NUM)
            ->column(C::DATETIME)
            ->column(C::CARRYING)
            ->column(C::NETTO)
            ->column(C::BRUTTO_NEAR_SIDE . ' - ' . C::BRUTTO_FAR_SIDE, null, $columnDiffSide)
            ->column(C::BRUTTO_FIRST_CARRIAGE . ' - ' . C::BRUTTO_SECOND_CARRIAGE, null, $columnDiffCarriage)
            ->table(T::VAN_DYNAMIC_BRUTTO)
            ->where(C::SCALE_NUM, Comparison::IN, $scaleNumsDyn);

        $builderCargo = clone $builder;
        $builderCargo
            ->column(C::VAN_NUMBER)
            ->table(T::VAN_DYNAMIC_BRUTTO)
            ->where(C::SCALE_NUM, Comparison::IN, $scaleNumsDyn);

        $builderSta = clone $builder;
        $builderSta
            ->column(C::VAN_NUMBER)
            ->column(C::SCALE_NUM)
            ->column(C::DATETIME)
            ->column(C::NETTO)
            ->where(C::SCALE_NUM, Comparison::IN, $scaleNumsSta)
            ->table(T::VAN_STATIC_BRUTTO);

        if ($this->dateStart) {
            $this->dateStartSta = date_sub(date_create()->setTimestamp($this->dateStart),
                new DateInterval('P' . $timePeriod . 'D'))->getTimestamp();

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

        $builderDyn->where(C::CARGO_TYPE, Comparison::EQUAL, $cargoTypeDyn);
        $builderCargo->where(C::CARGO_TYPE, Comparison::EQUAL, $cargoTypeDyn);

        $builderSta->where(C::VAN_NUMBER, Comparison::IN, $builderCargo);
        $builderSta->where(C::CARGO_TYPE, Comparison::EQUAL, $cargoTypeSta);

        $this->builder
            ->column(C::SCALE_NUM, A::STA, $columnScalesSta)
            ->column(C::DATETIME, A::STA, $columnDateTimeSta)
            ->column(C::VAN_NUMBER)
            ->column(C::CARRYING)
            ->column(C::NETTO, A::STA, $columnNettoSta)
            ->column(C::NETTO, A::DYN, $columnNettoDyn)
            ->column(A::DYN . '.' . C::NETTO . ' - ' . C::CARRYING, null, $columnDiffDynCarr)
            ->column(A::DYN . '.' . C::NETTO . ' - ' . A::STA . '.' . C::NETTO, null, $columnDiffDynSta)
            ->column($columnDiffSide)
            ->column($columnDiffCarriage)
            ->column(C::DATETIME, A::DYN, $columnDateTimeDyn)
            ->column(C::SCALE_NUM, A::DYN, $columnScalesDyn)
            ->table(sprintf(self::SUB_QUERY, $builderDyn->build()), A::DYN)
            ->join(sprintf(self::SUB_QUERY, $builderSta->build()), C::VAN_NUMBER, A::STA)
            ->order($columnScalesDyn)
            ->order($columnDateTimeDyn, true);
    }
}
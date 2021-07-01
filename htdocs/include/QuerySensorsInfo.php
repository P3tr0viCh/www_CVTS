<?php
require_once "builders/query_builder/Builder.php";
require_once "QueryBase.php";

use builders\query_builder\Expr as E;
use builders\query_builder\Builder as B;
use builders\query_builder\Comparison;
use database\Info as I;
use database\Tables as T;
use database\Columns as C;
use database\Aliases as A;

class QuerySensorsInfo extends QueryBase
{
    const SUB_QUERY = '(%s)';
    const SUB_QUERY_2 = '(%s) %s %s';
    const UNION = '%s UNION %s';

    private bool $showDisabled = false;

    public function setShowDisabled(int $showDisabled): static
    {
        $this->showDisabled = $showDisabled;
        return $this;
    }

    private function setColumnsArray(B $builder, $column, $count)
    {
        for ($i = 1; $i <= $count; $i++) {
            if (is_null($column)) $c = "'" . A::NU . "'"; else $c = $column . $i;
            $builder->column($c);
        }
    }

    protected function makeQuery()
    {
        $builderMaxBDateTime = B::getInstance();
        $builderMaxBDateTime
            ->column(C::SCALE_NUM)
            ->column(B::max(C::DATETIME), null, C::DATETIME);

        $builderMaxBDateTimeZ = clone $builderMaxBDateTime;
        $builderMaxBDateTimeT = clone $builderMaxBDateTime;
        $builderMaxBDateTimeS = clone $builderMaxBDateTime;

        $builderMaxBDateTimeZ
            ->table(T::SENSORS_ZEROS)
            ->group(C::SCALE_NUM);
        $builderMaxBDateTimeZI = clone $builderMaxBDateTimeZ;
        $builderMaxBDateTimeZ
            ->where(C::SENSORS_INIT, Comparison::EQUAL, 0);
        $builderMaxBDateTimeZI
            ->where(C::SENSORS_INIT, Comparison::EQUAL, 1);
        $builderMaxBDateTimeT->table(T::SENSORS_TEMPS)->group(C::SCALE_NUM);
        $builderMaxBDateTimeS->table(T::SENSORS_STATUS)->group(C::SCALE_NUM);

        $builderS = B::getInstance();
        $builderS
            ->column(C::SCALE_NUM)
            ->column(C::DATETIME)
            ->column('0', null, C::SENSORS_INFO_TYPE)
            ->table(sprintf(self::SUB_QUERY, $builderMaxBDateTimeS->build()), A::SENSORS_INFO_SENSORS_LAST)
            ->join(T::SENSORS_STATUS, array(C::SCALE_NUM, C::DATETIME));
        $this->setColumnsArray($builderS, C::SENSOR_M, Constants::SENSORS_M_MAX_COUNT);
        $this->setColumnsArray($builderS, C::SENSOR_T, Constants::SENSORS_T_MAX_COUNT);

        $builderT = B::getInstance();
        $builderT
            ->table(sprintf(self::SUB_QUERY, $builderMaxBDateTimeT->build()), A::SENSORS_INFO_TEMPS_LAST)
            ->join(T::SENSORS_TEMPS, array(C::SCALE_NUM, C::DATETIME));

        $builderZT = B::getInstance();
        $builderZT
            ->column(C::SCALE_NUM)
            ->column(C::DATETIME, A::SENSORS_INFO_ZEROS_VAL)
            ->column('1', null, C::SENSORS_INFO_TYPE)
            ->table(sprintf(self::SUB_QUERY, $builderMaxBDateTimeZ->build()), A::SENSORS_INFO_ZEROS_LAST)
            ->join(T::SENSORS_ZEROS, array(C::SCALE_NUM, C::DATETIME), A::SENSORS_INFO_ZEROS_VAL)
            ->join(sprintf(self::SUB_QUERY, $builderT->build()), C::SCALE_NUM, A::NU);
        $this->setColumnsArray($builderZT, C::SENSOR_M, Constants::SENSORS_M_MAX_COUNT);
        $this->setColumnsArray($builderZT, C::SENSOR_T, Constants::SENSORS_T_MAX_COUNT);

        $builderZI = B::getInstance();
        $builderZI
            ->column(C::SCALE_NUM)
            ->column(C::DATETIME)
            ->column('2', null, C::SENSORS_INFO_TYPE)
            ->table(sprintf(self::SUB_QUERY, $builderMaxBDateTimeZI->build()), A::SENSORS_INFO_ZEROS_INIT_LAST)
            ->join(T::SENSORS_ZEROS, array(C::SCALE_NUM, C::DATETIME), A::SENSORS_INFO_ZEROS_VAL);
        $this->setColumnsArray($builderZI, C::SENSOR_M, Constants::SENSORS_M_MAX_COUNT);
        $this->setColumnsArray($builderZI, null, Constants::SENSORS_T_MAX_COUNT);

        $builderSZT = B::getInstance();
        $builderSZT
            ->table(sprintf(self::UNION,
                sprintf(self::SUB_QUERY_2, $builderS->build(), E::EXPR_AS, A::NU),
                $builderZT->build()));

        $builderSZIT = B::getInstance();
        $builderSZIT
            ->table(sprintf(self::UNION,
                sprintf(self::SUB_QUERY_2, $builderSZT->build(), E::EXPR_AS, A::NU),
                $builderZI->build()));

        $this->builder
            ->column(C::SCALE_NUM)
            ->column(C::SCALE_PLACE)
            ->column(C::SENSORS_INFO_TYPE)
            ->column(C::DATETIME, null, C::DATETIME_SENSORS_INFO)
            ->table(sprintf(self::SUB_QUERY, $builderSZIT->build()), A::NU)
            ->join(T::SCALES, C::SCALE_NUM)
            ->join(T::SCALES_ADD, C::SCALE_NUM)
            ->order(C::SCALE_PLACE, false, I::COLLATE_LATIN)
            ->order(C::SENSORS_INFO_TYPE);
        $this->setColumnsArray($this->builder, C::SENSOR_M, Constants::SENSORS_M_MAX_COUNT);
        $this->setColumnsArray($this->builder, C::SENSOR_T, Constants::SENSORS_T_MAX_COUNT);

        if (!$this->showDisabled) {
            $this->builder
                ->where(C::SCALE_DISABLED, Comparison::EQUAL, false);
        }
    }
}
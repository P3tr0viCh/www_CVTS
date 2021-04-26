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

    private function setColumns(B $builder)
    {
        for ($i = 1; $i <= Constants::SENSORS_M_MAX_COUNT; $i++) {
            $builder->column(C::SENSOR_M . $i);
        }
        for ($i = 1; $i <= Constants::SENSORS_T_MAX_COUNT; $i++) {
            $builder->column(C::SENSOR_T . $i);
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

        $builderMaxBDateTimeZ->table(T::SENSORS_ZEROS)->group(C::SCALE_NUM);
        $builderMaxBDateTimeT->table(T::SENSORS_TEMPS)->group(C::SCALE_NUM);
        $builderMaxBDateTimeS->table(T::SENSORS_STATUS)->group(C::SCALE_NUM);

        $builderS = B::getInstance();
        $builderS
            ->column(C::SCALE_NUM)
            ->column(C::DATETIME)
            ->column('0', null, A::SENSOR_INFO_TYPE)
            ->table(sprintf(self::SUB_QUERY, $builderMaxBDateTimeS->build()), A::SENSORS_INFO_SENSORS_LAST)
            ->join(T::SENSORS_STATUS, array(C::SCALE_NUM, C::DATETIME));
        $this->setColumns($builderS);

        $builderZ = B::getInstance();
        $builderZ
            ->table(sprintf(self::SUB_QUERY, $builderMaxBDateTimeZ->build()), A::SENSORS_INFO_ZEROS_LAST)
            ->join(T::SENSORS_ZEROS, array(C::SCALE_NUM, C::DATETIME), A::SENSORS_INFO_ZEROS_LAST);
        $builderT = B::getInstance();
        $builderT
            ->table(sprintf(self::SUB_QUERY, $builderMaxBDateTimeT->build()), A::SENSORS_INFO_TEMPS_LAST)
            ->join(T::SENSORS_TEMPS, array(C::SCALE_NUM, C::DATETIME));

        $builderZT = B::getInstance();
        $builderZT
            ->column(C::SCALE_NUM)
            ->column(C::DATETIME, A::SENSORS_INFO_ZEROS_VAL)
            ->column('1', null, A::SENSOR_INFO_TYPE)
            ->table(sprintf(self::SUB_QUERY, $builderMaxBDateTimeZ->build()), A::SENSORS_INFO_ZEROS_LAST)
            ->join(T::SENSORS_ZEROS, array(C::SCALE_NUM, C::DATETIME), A::SENSORS_INFO_ZEROS_VAL)
            ->join(sprintf(self::SUB_QUERY, $builderT->build()), C::SCALE_NUM, A::NU);
        $this->setColumns($builderZT);

        $builderSZT = B::getInstance();
        $builderSZT
            ->table(sprintf(self::UNION,
                sprintf(self::SUB_QUERY_2, $builderS->build(), E::EXPR_AS, A::NU),
                $builderZT->build()));

        $this->builder
            ->column(C::SCALE_NUM)
            ->column(C::SCALE_PLACE)
            ->column(C::DATETIME)
            ->table(sprintf(self::SUB_QUERY, $builderSZT->build()), A::NU)
            ->join(T::SCALES, C::SCALE_NUM)
            ->join(T::SCALES_ADD, C::SCALE_NUM)
            ->order(C::SCALE_PLACE, false, I::COLLATE_LATIN)
            ->order(A::SENSOR_INFO_TYPE);
        $this->setColumns($this->builder);

        if (!$this->showDisabled) {
            $this->builder
                ->where(C::SCALE_DISABLED, Comparison::EQUAL, false);
        }
    }
}
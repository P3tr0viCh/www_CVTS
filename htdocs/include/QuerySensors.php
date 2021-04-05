<?php
require_once "builders/query_builder/Builder.php";
require_once "QueryBase.php";

use JetBrains\PhpStorm\Pure;
use QueryBuilder\Builder as B;
use database\Info as I;
use database\Tables as T;
use database\Columns as C;

class QuerySensors extends QueryBase
{
    const MYSQL_DATETIME_FORMAT = "YmdHis";

    private ?int $scaleNum = null;

    private ?int $dateTimeStart = null;
    private ?int $dateTimeEnd = null;

    private int $resultType;

    private int $sensorsMCount = 0;
    private int $sensorsTCount = 0;

    public function setScaleNum(?int $scaleNum): static
    {
        $this->scaleNum = (int)$scaleNum;
        return $this;
    }

    public function setDateTimeStart(?int $dateTimeStart): static
    {
        $this->dateTimeStart = $dateTimeStart;
        return $this;
    }

    public function setDateTimeEnd(?int $dateTimeEnd): static
    {
        $this->dateTimeEnd = $dateTimeEnd;
        return $this;
    }

    public function setResultType(int $resultType): static
    {
        $this->resultType = $resultType;
        return $this;
    }

    public function setSensorsMCount(int $sensorsMCount): static
    {
        $this->sensorsMCount = $sensorsMCount == 0 || $sensorsMCount > Constants::SENSORS_M_MAX_COUNT ? Constants::SENSORS_M_MAX_COUNT : $sensorsMCount;
        return $this;
    }

    public function setSensorsTCount(int $sensorsTCount): static
    {
        $this->sensorsTCount = $sensorsTCount == 0 || $sensorsTCount > Constants::SENSORS_T_MAX_COUNT ? Constants::SENSORS_T_MAX_COUNT : $sensorsTCount;
        return $this;
    }

    #[Pure] private function isAllScales(): bool {
        return $this->scaleNum == Constants::SCALE_NUM_ALL_TRAIN_SCALES;
    }

    #[Pure] private function getTableName(): string
    {
        return match ($this->resultType) {
            ResultType::SENSORS_ZEROS => T::SENSORS_ZEROS,
            ResultType::SENSORS_TEMPS => T::SENSORS_TEMPS,
            ResultType::SENSORS_STATUS => T::SENSORS_STATUS,
            default => throw new InvalidArgumentException("Unknown resultType ($this->resultType)"),
        };
    }

    private function setColumns()
    {
        if ($this->isAllScales()) {
            $this->builder->column(C::SCALE_NUM);
            $this->builder->column(C::SCALE_PLACE);
        }

        $this->builder->column(C::DATETIME);

        if ($this->resultType == ResultType::SENSORS_STATUS or
            $this->resultType == ResultType::SENSORS_ZEROS) {
            for ($i = 1; $i <= $this->sensorsMCount; $i++) {
                $this->builder->column(C::SENSOR_M . $i);
            }
        }
        if ($this->resultType == ResultType::SENSORS_STATUS or
            $this->resultType == ResultType::SENSORS_TEMPS) {
            for ($i = 1; $i <= $this->sensorsTCount; $i++) {
                $this->builder->column(C::SENSOR_T . $i);
            }
        }
    }

    private function setOrder() {
        if ($this->isAllScales()) {
            $this->builder->order(C::SCALE_PLACE, false, I::COLLATE_LATIN);
        }

        $this->builder->order(C::DATETIME, true);
    }

    protected function makeQuery()
    {
        $this->builder->table($this->getTableName());

        $this->setColumns();

        $dateTimeStart = $this->dateTimeStart;
        $dateTimeEnd = $this->dateTimeEnd;

        if ($this->scaleNum != Constants::SCALE_NUM_ALL_TRAIN_SCALES) {
            $this->builder->where(C::SCALE_NUM, B::COMPARISON_EQUAL, $this->scaleNum);
        }

        if ($dateTimeStart) {
            $dateTimeStart = (float)date(self::MYSQL_DATETIME_FORMAT, $dateTimeStart);
        }
        if ($dateTimeEnd) {
            $dateTimeEnd = (float)date(self::MYSQL_DATETIME_FORMAT, $dateTimeEnd);
        }

        if ($dateTimeStart) {
            $this->builder
                ->where(C::DATETIME, B::COMPARISON_GREATER_OR_EQUAL, $dateTimeStart);
        }
        if ($dateTimeEnd) {
            $this->builder
                ->where(C::DATETIME, B::COMPARISON_LESS_OR_EQUAL, $dateTimeEnd);
        }

        if ($this->isAllScales()) {
            $this->builder->join(T::SCALES, C::SCALE_NUM);
        }

        $this->setOrder();
    }
}
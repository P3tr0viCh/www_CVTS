<?php
require_once "builders/query_builder/Builder.php";
require_once "QueryBaseDates.php";

use JetBrains\PhpStorm\Pure;
use builders\query_builder\Comparison;
use database\Info as I;
use database\Tables as T;
use database\Columns as C;

class QuerySensors extends QueryBaseDates
{
    private int $scaleNum = Constants::SCALE_NUM_ALL_TRAIN_SCALES;

    private ?string $scalesFilter = null;

    private int $resultType;

    private int $sensorsMCount = 0;
    private int $sensorsTCount = 0;

    private bool $showDisabled = false;

    public function setScaleNum(int $scaleNum): static
    {
        $this->scaleNum = $scaleNum;
        return $this;
    }

    public function setScalesFilter(?string $scalesFilter): static
    {
        $this->scalesFilter = $scalesFilter;
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

    public function setShowDisabled(int $showDisabled): static
    {
        $this->showDisabled = $showDisabled;
        return $this;
    }

    #[Pure] private function isAllScales(): bool
    {
        return $this->scaleNum == Constants::SCALE_NUM_ALL_TRAIN_SCALES;
    }

    private function getTableName(): string
    {
        return match ($this->resultType) {
            ResultType::SENSORS_ZEROS => T::SENSORS_ZEROS,
            ResultType::SENSORS_TEMPS => T::SENSORS_TEMPS,
            ResultType::SENSORS_STATUS => T::SENSORS_STATUS,
            default => throw new InvalidArgumentException("Unknown resultType ($this->resultType)"),
        };
    }

    private function setTable()
    {
        $this->builder->table($this->getTableName());
    }

    private function setColumns()
    {
        if ($this->isAllScales()) {
            $this->builder->column(C::SCALE_NUM);
            $this->builder->column(C::SCALE_PLACE);
        }

        $this->builder->column(C::DATETIME);

        if ($this->resultType == ResultType::SENSORS_STATUS ||
            $this->resultType == ResultType::SENSORS_ZEROS) {
            for ($i = 1; $i <= $this->sensorsMCount; $i++) {
                $this->builder->column(C::SENSOR_M . $i);
            }
        }
        if ($this->resultType == ResultType::SENSORS_STATUS ||
            $this->resultType == ResultType::SENSORS_TEMPS) {
            for ($i = 1; $i <= $this->sensorsTCount; $i++) {
                $this->builder->column(C::SENSOR_T . $i);
            }
        }
        if ($this->resultType == ResultType::SENSORS_ZEROS) {
            $this->builder->column(C::SENSORS_INIT);
        }
    }

    private function setJoin()
    {
        if ($this->isAllScales()) {
            $this->builder->join(T::SCALES, C::SCALE_NUM);
            $this->builder->join(T::SCALES_ADD, C::SCALE_NUM);
        }
    }

    private function setWhere()
    {
        if ($this->isAllScales()) {
            $this->builder->where(C::SCALE_NUM, Comparison::IN, $this->scalesFilter);
        } else {
            $this->builder->where(C::SCALE_NUM, Comparison::EQUAL, $this->scaleNum);
        }

        $this->builder
            ->where(C::DATETIME, Comparison::GREATER_OR_EQUAL, $this->getDateTimeStart())
            ->where(C::DATETIME, Comparison::LESS_OR_EQUAL, $this->getDateTimeEnd());

        if ($this->isAllScales() and !$this->showDisabled) {
            $this->builder
                ->where(C::SCALE_DISABLED, Comparison::EQUAL, false);
        }
    }

    private function setOrder()
    {
        if ($this->isAllScales()) {
            $this->builder->order(C::SCALE_PLACE, false, I::COLLATE_LATIN);
        }

        $this->builder->order(C::DATETIME, true);
    }

    protected function makeQuery()
    {
        $this->setTable();
        $this->setColumns();
        $this->setJoin();
        $this->setWhere();
        $this->setOrder();
    }
}
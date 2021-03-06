<?php
require_once "builders/query_builder/Builder.php";
require_once "QueryBase.php";

require_once "ResultFilter.php";

use builders\query_builder\Builder as B;
use builders\query_builder\Comparison;
use database\Info as I;
use database\Tables as T;
use database\Columns as C;

class QueryResult extends QueryBase
{
    const MYSQL_DATETIME_FORMAT = "YmdHis";

    private ResultFilter $filter;

    private int $scaleType;
    private int $resultType;

    public function setScaleType(int $scaleType): static
    {
        $this->scaleType = $scaleType;
        return $this;
    }

    public function setResultType(int $resultType): static
    {
        $this->resultType = $resultType;
        return $this;
    }

    public function setFilter(ResultFilter $filter): static
    {
        $this->filter = $filter;
        return $this;
    }

    private bool $showCargoDate;
    private bool $showDeltas;
    private bool $showDeltasMi3115;

    private function getTableName(): string
    {
        return match ($this->resultType) {
            ResultType::TRAIN_DYNAMIC => T::TRAIN_DYNAMIC,
            ResultType::TRAIN_DYNAMIC_ONE,
            ResultType::VAN_DYNAMIC_BRUTTO,
            ResultType::COMPARE_DYNAMIC,
            ResultType::CARGO_LIST_DYNAMIC => T::VAN_DYNAMIC_BRUTTO,
            ResultType::VAN_DYNAMIC_TARE => T::VAN_DYNAMIC_TARE,
            ResultType::VAN_STATIC_BRUTTO,
            ResultType::COMPARE_STATIC,
            ResultType::CARGO_LIST_STATIC => T::VAN_STATIC_BRUTTO,
            ResultType::VAN_STATIC_TARE => T::VAN_STATIC_TARE,
            ResultType::AUTO_BRUTTO, ResultType::CARGO_LIST_AUTO => T::AUTO_BRUTTO,
            ResultType::AUTO_TARE => T::AUTO_TARE,
            ResultType::KANAT => T::KANAT,
            ResultType::DP, ResultType::DP_SUM => T::DP,
            default => throw new InvalidArgumentException("Unknown resultType ($this->resultType)"),
        };
    }

    private function setColumns()
    {
        switch ($this->resultType) {
            case ResultType::TRAIN_DYNAMIC:
            case ResultType::TRAIN_DYNAMIC_ONE:
            case ResultType::VAN_DYNAMIC_BRUTTO:
            case ResultType::VAN_DYNAMIC_TARE:
            case ResultType::VAN_STATIC_BRUTTO:
            case ResultType::VAN_STATIC_TARE:
            case ResultType::AUTO_BRUTTO:
            case ResultType::AUTO_TARE:
            case ResultType::KANAT:
            case ResultType::DP:
                if ($this->filter->isFull()) {
                    return;
                }
        }

        switch ($this->resultType) {
            case ResultType::TRAIN_DYNAMIC:
            case ResultType::TRAIN_DYNAMIC_ONE:
            case ResultType::VAN_DYNAMIC_BRUTTO:
            case ResultType::VAN_DYNAMIC_TARE:
            case ResultType::VAN_STATIC_BRUTTO:
            case ResultType::VAN_STATIC_TARE:
            case ResultType::COMPARE_DYNAMIC:
            case ResultType::COMPARE_STATIC:
                if ($this->filter->getScaleNum() == Constants::SCALE_NUM_ALL_TRAIN_SCALES) {
                    $this->builder->column(C::SCALE_NUM);
                }
        }

        switch ($this->resultType) {
            case ResultType::TRAIN_DYNAMIC_ONE:
            case ResultType::VAN_DYNAMIC_BRUTTO:
            case ResultType::VAN_STATIC_BRUTTO:
                $this->builder
                    ->column(C::SEQUENCE_NUMBER)
                    ->column(C::DATETIME)
                    ->column(C::VAN_NUMBER)
                    ->column(C::VAN_TYPE)
                    ->column(C::CARRYING)
                    ->column(C::BRUTTO)
                    ->column($this->showDeltas ? C::MI_DELTA_ABS_BRUTTO : null)
                    ->column(C::TARE)
                    ->column($this->showDeltas ? C::MI_DELTA_ABS_TARE : null)
                    ->column(C::NETTO)
                    ->column($this->showDeltas ? C::MI_DELTA : null);

                if ($this->showDeltasMi3115) {
                    $this->builder
                        ->column(C::OVERLOAD)
                        ->column(C::MI_3115_DELTA)
                        ->column(C::MI_3115_TOLERANCE)
                        ->column(C::MI_3115_RESULT);
                }

                if ($this->scaleType == ScaleType::WMR) {
                    $this->builder
                        ->column(C::INVOICE_TARE)
                        ->column(C::INVOICE_NETTO)
                        ->column(C::INVOICE_OVERLOAD);
                }

                $this->builder
                    ->column(C::CARGO_TYPE)
                    ->column($this->showCargoDate ? C::DATETIME_CARGO : null)
                    ->column($this->showDeltas ? C::MI_TARE_DYN : null)
                    ->column($this->filter->isFull() && $this->showDeltas ?
                        C::MI_TARE_DYN_SCALES : null)
                    ->column($this->filter->isFull() && $this->showDeltas ? C::MI_TARE_DYN_DATETIME : null)
                    ->column($this->showDeltas ? C::MI_DELTA_ABS_TARE_DYN : null)
                    ->column($this->showDeltas ? C::MI_DELTA_DYN : null)
                    ->column($this->showDeltas ? C::MI_TARE_STA : null)
                    ->column($this->filter->isFull() && $this->showDeltas ? C::MI_TARE_STA_SCALES : null)
                    ->column($this->filter->isFull() && $this->showDeltas ?
                        C::MI_TARE_STA_DATETIME : null)
                    ->column($this->showDeltas ? C::MI_DELTA_ABS_TARE_STA : null)
                    ->column($this->showDeltas ? C::MI_DELTA_STA : null)
                    ->column(C::INVOICE_NUMBER)
                    ->column(C::INVOICE_SUPPLIER)
                    ->column(C::INVOICE_RECIPIENT)
                    ->column(C::DEPART_STATION)
                    ->column(C::PURPOSE_STATION)
                    ->column(C::OPERATOR);
                break;
            case ResultType::VAN_DYNAMIC_TARE:
            case ResultType::VAN_STATIC_TARE:
                $this->builder
                    ->column(C::DATETIME)
                    ->column(C::VAN_NUMBER)
                    ->column(C::VAN_TYPE)
                    ->column(C::TARE)
                    ->column(C::OPERATOR);
                break;
            case ResultType::TRAIN_DYNAMIC:
                $this->builder
                    ->column(C::DATETIME)
                    ->column(C::TRAIN_NUMBER)
                    ->column(C::BRUTTO)
                    ->column(C::TARE)
                    ->column(C::NETTO)
                    ->column(C::VAN_COUNT)
                    ->column(C::OPERATOR)
                    ->column(C::TRAIN_NUM)
                    ->column(C::UNIX_TIME);
                break;
            case ResultType::AUTO_BRUTTO:
                $this->builder
                    ->column(C::DATETIME)
                    ->column(C::AUTO_NUMBER)
                    ->column(C::INVOICE_NETTO)
                    ->column(C::BRUTTO)
                    ->column(C::TARE)
                    ->column(C::NETTO)
                    ->column(C::CARGO_TYPE)
                    ->column(C::INVOICE_NUMBER)
                    ->column(C::INVOICE_SUPPLIER)
                    ->column(C::INVOICE_RECIPIENT)
                    ->column(C::OPERATOR);
                break;
            case ResultType::AUTO_TARE:
                $this->builder
                    ->column(C::AUTO_NUMBER)
                    ->column(C::TARE)
                    ->column(C::DATETIME)
                    ->column(C::DRIVER)
                    ->column(C::OPERATOR);
                break;
            case ResultType::DP:
                $this->builder
                    ->column(C::DATETIME)
                    ->column(C::PRODUCT)
                    ->column(C::LEFT_SIDE)
                    ->column(C::NETTO);
                break;
            case ResultType::DP_SUM:
                $this->builder
                    ->column(C::PRODUCT)
                    ->column(B::sum(C::NETTO), null, C::NETTO);
                break;
            case ResultType::KANAT:
                $this->builder
                    ->column(C::DATETIME)
                    ->column(C::BRUTTO)
                    ->column(C::TARE)
                    ->column(C::NETTO);
                break;
            case ResultType::COMPARE_DYNAMIC:
            case ResultType::COMPARE_STATIC:
                $this->builder
                    ->column(C::SEQUENCE_NUMBER)
                    ->column(C::UNIX_TIME)
                    ->column(C::DATETIME)
                    ->column(C::VAN_NUMBER)
                    ->column(C::CARRYING)
                    ->column($this->filter->isCompareByBrutto() ?
                        C::BRUTTO :
                        C::NETTO)
                    ->column(C::NETTO . "-" . C::CARRYING, null, C::OVERLOAD)
                    ->column(C::BRUTTO_NEAR_SIDE . "-" . C::BRUTTO_FAR_SIDE, null, C::SIDE_DIFFERENCE)
                    ->column(C::BRUTTO_FIRST_CARRIAGE . "-" . C::BRUTTO_SECOND_CARRIAGE, null, C::CARRIAGE_DIFFERENCE);
                break;
            case ResultType::CARGO_LIST_DYNAMIC:
            case ResultType::CARGO_LIST_STATIC:
            case ResultType::CARGO_LIST_AUTO:
                $this->builder
                    ->column(C::CARGO_TYPE)
                    ->column($this->filter->isFull() ? B::count(C::CARGO_TYPE) : null,
                        null, C::COUNT);
                break;
        }
    }

    private function setWhere()
    {
        $dateTimeColumn = match ($this->resultType) {
            ResultType::COEFFS => C::DATETIME_END,
            default => C::DATETIME,
        };

        if ($this->filter->getScaleNum() != Constants::SCALE_NUM_ALL_TRAIN_SCALES) {
            $this->builder->where(C::SCALE_NUM, Comparison::EQUAL, $this->filter->getScaleNum());
        }

        $dateTimeStart = $this->filter->getDateTimeStart();
        $dateTimeEnd = $this->filter->getDateTimeEnd();

        if ($dateTimeStart) {
            $dateTimeStart = (int)date(self::MYSQL_DATETIME_FORMAT, $dateTimeStart);
        }
        if ($dateTimeEnd) {
            $dateTimeEnd = (int)date(self::MYSQL_DATETIME_FORMAT, $dateTimeEnd);
        }

        if ($dateTimeStart) {
            $this->builder
                ->where($dateTimeColumn, Comparison::GREATER_OR_EQUAL, $dateTimeStart);
        }
        if ($dateTimeEnd) {
            $this->builder
                ->where($dateTimeColumn, Comparison::LESS_OR_EQUAL, $dateTimeEnd);
        }

        switch ($this->resultType) {
            case ResultType::AUTO_BRUTTO:
            case ResultType::AUTO_TARE:
            case ResultType::VAN_DYNAMIC_BRUTTO:
            case ResultType::VAN_DYNAMIC_TARE:
            case ResultType::VAN_STATIC_BRUTTO:
            case ResultType::VAN_STATIC_TARE:
            case ResultType::COMPARE_DYNAMIC:
            case ResultType::COMPARE_STATIC:
                $this->builder
                    ->where($this->scaleType == ScaleType::AUTO ?
                        C::AUTO_NUMBER :
                        C::VAN_NUMBER, Comparison::LIKE,
                        utf8ToLatin1($this->filter->getVanNumber()));
        }

        switch ($this->resultType) {
            case ResultType::TRAIN_DYNAMIC_ONE:
            case ResultType::VAN_DYNAMIC_BRUTTO:
            case ResultType::VAN_STATIC_BRUTTO:
            case ResultType::AUTO_BRUTTO:
            case ResultType::CARGO_LIST_AUTO:
            case ResultType::CARGO_LIST_DYNAMIC:
            case ResultType::CARGO_LIST_STATIC:
            case ResultType::COMPARE_DYNAMIC:
            case ResultType::COMPARE_STATIC:
                $this->builder
                    ->where(C::CARGO_TYPE, Comparison::LIKE,
                        utf8ToLatin1($this->filter->getCargoType()));
        }

        switch ($this->resultType) {
            case ResultType::TRAIN_DYNAMIC_ONE:
            case ResultType::VAN_DYNAMIC_BRUTTO:
            case ResultType::VAN_STATIC_BRUTTO:
            case ResultType::AUTO_BRUTTO:
            case ResultType::COMPARE_DYNAMIC:
            case ResultType::COMPARE_STATIC:
                $this->builder
                    ->where(C::INVOICE_NUMBER, Comparison::LIKE,
                        utf8ToLatin1($this->filter->getInvoiceNum()))
                    ->where(C::INVOICE_SUPPLIER, Comparison::LIKE,
                        utf8ToLatin1($this->filter->getInvoiceSupplier()))
                    ->where(C::INVOICE_RECIPIENT, Comparison::LIKE,
                        utf8ToLatin1($this->filter->getInvoiceRecipient()));
        }

        $this->builder->where(C::SCALE_NUM, Comparison::IN, $this->filter->getScalesFilter());

        switch ($this->resultType) {
            case ResultType::DP:
            case ResultType::DP_SUM:
                if ($this->filter->isOnlyChark()) {
                    $this->builder->where(C::PRODUCT, Comparison::EQUAL,
                        utf8ToLatin1(CargoTypes::CHARK));
                }
        }

        switch ($this->resultType) {
            case ResultType::TRAIN_DYNAMIC_ONE:
                $this->builder
                    ->where(C::TRAIN_NUM, Comparison::EQUAL,
                        $this->filter->getTrainNum())
                    ->where(C::UNIX_TIME, Comparison::EQUAL,
                        $this->filter->getTrainUnixTime());
                break;
            case ResultType::CARGO_LIST_AUTO:
            case ResultType::CARGO_LIST_DYNAMIC:
            case ResultType::CARGO_LIST_STATIC:
                $this->builder->where(C::CARGO_TYPE, Comparison::NOT_EQUAL, '');
                break;
            case ResultType::COMPARE_DYNAMIC:
            case ResultType::COMPARE_STATIC:
                if (!$this->filter->isCompareByBrutto()) {
                    $this->builder->where(C::NETTO, Comparison::NOT_EQUAL, 0);
                }
        }
    }

    private function setOrder()
    {
        if (isResultTypeCargoList($this->resultType)) {
            $this->builder->order(C::CARGO_TYPE, false, I::COLLATE_LATIN);
        } else {
            $orderByDesc = match ($this->resultType) {
                ResultType::DP, ResultType::KANAT => $this->filter->isOrderByDesc(),
                default => true
            };

            $this->builder->order(C::DATETIME, $orderByDesc);

            if (($this->filter->getScaleNum() != Constants::SCALE_NUM_ALL_TRAIN_SCALES) &&
                ($this->resultType == ResultType::VAN_DYNAMIC_BRUTTO ||
                    $this->resultType == ResultType::VAN_DYNAMIC_TARE ||
                    $this->resultType == ResultType::VAN_STATIC_BRUTTO ||
                    $this->resultType == ResultType::VAN_STATIC_TARE)
            ) {
                $this->builder->order(C::SEQUENCE_NUMBER);
            }
        }
    }

    private function setGroup()
    {
        switch ($this->resultType) {
            case ResultType::CARGO_LIST_AUTO:
            case ResultType::CARGO_LIST_STATIC:
            case ResultType::CARGO_LIST_DYNAMIC:
                $this->builder->group(C::CARGO_TYPE);
                break;
            case ResultType::DP_SUM:
                $this->builder->group(C::PRODUCT);
                break;
        }
    }

    protected function makeQuery()
    {
        $this->showCargoDate = $this->filter->isShowCargoDate() &&
            $this->resultType == ResultType::VAN_DYNAMIC_BRUTTO;
        $this->showDeltas = $this->filter->isShowDeltas() &&
            ($this->resultType == ResultType::TRAIN_DYNAMIC_ONE ||
                $this->resultType == ResultType::VAN_DYNAMIC_BRUTTO ||
                $this->resultType == ResultType::VAN_STATIC_BRUTTO);
        $this->showDeltasMi3115 = $this->filter->isShowDeltasMi3115() &&
            ($this->resultType == ResultType::TRAIN_DYNAMIC_ONE ||
                $this->resultType == ResultType::VAN_DYNAMIC_BRUTTO ||
                $this->resultType == ResultType::VAN_STATIC_BRUTTO);

        $this->builder
            ->params(B::SELECT_SQL_BUFFER_RESULT)
            ->table($this->getTableName());

        $this->setWhere();

        $this->setColumns();

        if ($this->showCargoDate) {
            $this->builder->join(T::VAN_BRUTTO_ADD,
                array(
                    C::TRAIN_NUM,
                    C::SCALE_NUM,
                    C::SEQUENCE_NUMBER,
                    C::UNIX_TIME));
        }

        if ($this->showDeltas) {
            $this->builder->join(T::VAN_DELTAS,
                array(
                    C::TRAIN_NUM,
                    C::SCALE_NUM,
                    C::SEQUENCE_NUMBER,
                    C::UNIX_TIME));
        }

        if ($this->showDeltasMi3115) {
            $this->builder->join(T::VAN_DELTAS_MI_3115,
                array(
                    C::TRAIN_NUM,
                    C::SCALE_NUM,
                    C::SEQUENCE_NUMBER,
                    C::UNIX_TIME));
        }

        $this->setGroup();

        $this->setOrder();
    }
}
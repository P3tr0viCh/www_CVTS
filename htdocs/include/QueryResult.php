<?php
require_once "builders/query_builder/Builder.php";
require_once "Database.php";
require_once "QueryBase.php";

require_once "ResultFilter.php";

use QueryBuilder\Builder as B;
use Database\Tables as T;
use Database\Columns as C;

class QueryResult extends QueryBase
{
    const MYSQL_DATETIME_FORMAT = "YmdHis";

    /**
     * @var ResultFilter
     */
    private $filter;

    /**
     * @param int $scaleType
     */
    public function setScaleType($scaleType)
    {
        $this->scaleType = $scaleType;
    }

    /**
     * @var int
     * @see ScaleType
     */
    private $scaleType;

    /**
     * @var int
     * @see ReportType;
     */
    private $resultType;

    /**
     * @param int $resultType
     */
    public function setResultType($resultType)
    {
        $this->resultType = $resultType;
    }

    /**
     * @param ResultFilter $filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    private $showCargoDate;
    private $showDeltas;

    private function getTableName()
    {
        switch ($this->resultType) {
            case ResultType::VAN_DYNAMIC_BRUTTO:
                return T::VAN_DYNAMIC_BRUTTO;
            case ResultType::VAN_DYNAMIC_TARE:
                return T::VAN_DYNAMIC_TARE;
            case ResultType::VAN_STATIC_BRUTTO:
                return T::VAN_STATIC_BRUTTO;
            case ResultType::VAN_STATIC_TARE:
                return T::VAN_STATIC_TARE;

            case ResultType::TRAIN_DYNAMIC:
                return T::TRAIN_DYNAMIC;
            case ResultType::TRAIN_DYNAMIC_ONE:
                return T::VAN_DYNAMIC_BRUTTO;

            case ResultType::AUTO_BRUTTO:
                return T::AUTO_BRUTTO;
            case ResultType::AUTO_TARE:
                return T::AUTO_TARE;

            case ResultType::KANAT:
                return T::KANAT;

            case ResultType::DP:
            case ResultType::DP_SUM:
                return T::DP;

            case ResultType::CARGO_LIST_DYNAMIC:
                return T::VAN_DYNAMIC_BRUTTO;
            case ResultType::CARGO_LIST_STATIC:
                return T::VAN_STATIC_BRUTTO;
            case ResultType::CARGO_LIST_AUTO:
                return T::AUTO_BRUTTO;
            case ResultType::COMPARE_DYNAMIC:
                return T::VAN_DYNAMIC_BRUTTO;
            case ResultType::COMPARE_STATIC:
                return T::VAN_STATIC_BRUTTO;

            case ResultType::COEFFS:
                return T::COEFFS;

            default:
                throw new InvalidArgumentException("Unknown resultType ($this->resultType)");
        }
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
            case ResultType::COEFFS:
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
                    ->column(C::SUM_NETTO, null, C::NETTO);
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
                $this->builder->column(C::CARGO_TYPE);
                break;
            case ResultType::COEFFS:
                $this->builder
                    ->column(C::DATETIME_END)
                    ->column(C::COEFFICIENT_P1)
                    ->column(C::COEFFICIENT_Q1)
                    ->column(C::TEMPERATURE_1, null, C::COEFFICIENT_T1)
                    ->column(C::COEFFICIENT_P2)
                    ->column(C::COEFFICIENT_Q2)
                    ->column(C::TEMPERATURE_2, null, C::COEFFICIENT_T2);
                break;
        }
    }

    private function setWhere()
    {
        switch ($this->resultType) {
            case ResultType::COEFFS:
                $dateTimeColumn = C::DATETIME_END;
                break;
            default:
                $dateTimeColumn = C::DATETIME;
        }

        if ($this->filter->getScaleNum() != Constants::SCALE_NUM_ALL_TRAIN_SCALES) {
            $this->builder->where(C::SCALE_NUM, B::COMPARISON_EQUAL, $this->filter->getScaleNum());
        }

        $dateTimeStart = $this->filter->getDateTimeStart();
        $dateTimeEnd = $this->filter->getDateTimeEnd();

        if ($dateTimeStart) {
            $dateTimeStart = (float)date(self::MYSQL_DATETIME_FORMAT, $dateTimeStart);
        }
        if ($dateTimeEnd) {
            $dateTimeEnd = (float)date(self::MYSQL_DATETIME_FORMAT, $dateTimeEnd);
        }

        if ($dateTimeStart) {
            $this->builder
                ->where($dateTimeColumn, B::COMPARISON_GREATER_OR_EQUAL, $dateTimeStart);
        }
        if ($dateTimeEnd) {
            $this->builder
                ->where($dateTimeColumn, B::COMPARISON_LESS_OR_EQUAL, $dateTimeEnd);
        }

        switch ($this->resultType) {
            case ResultType::AUTO_BRUTTO:
            case ResultType::AUTO_TARE:
            case ResultType::VAN_DYNAMIC_BRUTTO:
            case ResultType::VAN_DYNAMIC_TARE:
            case ResultType::VAN_STATIC_BRUTTO:
            case ResultType::VAN_STATIC_TARE:
                $this->builder
                    ->where($this->scaleType == ScaleType::AUTO ?
                        C::AUTO_NUMBER :
                        C::VAN_NUMBER, B::COMPARISON_LIKE,
                        utf8ToLatin1($this->filter->getVanNumber()));
        }

        switch ($this->resultType) {
            case ResultType::TRAIN_DYNAMIC_ONE:
            case ResultType::VAN_DYNAMIC_BRUTTO:
            case ResultType::VAN_STATIC_BRUTTO:
            case ResultType::AUTO_BRUTTO:
            case ResultType::COMPARE_DYNAMIC:
            case ResultType::COMPARE_STATIC:
                $this->builder
                    ->where(C::CARGO_TYPE, B::COMPARISON_LIKE,
                        utf8ToLatin1($this->filter->getCargoType()))
                    ->where(C::INVOICE_NUMBER, B::COMPARISON_LIKE,
                        utf8ToLatin1($this->filter->getInvoiceNum()))
                    ->where(C::INVOICE_SUPPLIER, B::COMPARISON_LIKE,
                        utf8ToLatin1($this->filter->getInvoiceSupplier()))
                    ->where(C::INVOICE_RECIPIENT, B::COMPARISON_LIKE,
                        utf8ToLatin1($this->filter->getInvoiceRecipient()));
        }

        $this->builder->where(C::SCALE_NUM, B::COMPARISON_IN, $this->filter->getScalesFilter());

        switch ($this->resultType) {
            case ResultType::DP:
            case ResultType::DP_SUM:
                if ($this->filter->isOnlyChark()) {
                    $this->builder->where(C::PRODUCT, B::COMPARISON_EQUAL,
                        utf8ToLatin1('Кокс'));
                }
        }

        switch ($this->resultType) {
            case ResultType::TRAIN_DYNAMIC_ONE:
                $this->builder
                    ->where(C::TRAIN_NUM, B::COMPARISON_EQUAL,
                        $this->filter->getTrainNum())
                    ->where(C::UNIX_TIME, B::COMPARISON_EQUAL,
                        $this->filter->getTrainUnixTime());
                break;
            case ResultType::CARGO_LIST_AUTO:
            case ResultType::CARGO_LIST_DYNAMIC:
            case ResultType::CARGO_LIST_STATIC:
                $this->builder->where(C::CARGO_TYPE, B::COMPARISON_NOT_EQUAL, '');
                break;
            case ResultType::COMPARE_DYNAMIC:
            case ResultType::COMPARE_STATIC:
                if (!$this->filter->isCompareByBrutto()) {
                    $this->builder->where(C::NETTO, B::COMPARISON_NOT_EQUAL, 0);
                }
        }

        if (($this->resultType == ResultType::COEFFS) && (!$this->filter->isFull())) {
            $innerBuilder = \QueryBuilder\Builder::getInstance();
            $innerBuilder
                ->column('max(' . C::DATETIME_END . ')')
                ->table(T::COEFFS);
            if ($this->filter->getScaleNum() != Constants::SCALE_NUM_ALL_TRAIN_SCALES) {
                $innerBuilder->where(C::SCALE_NUM, B::COMPARISON_EQUAL, $this->filter->getScaleNum());
            } else {
                $innerBuilder->where(C::SCALE_NUM, B::COMPARISON_IN, $this->filter->getScalesFilter());
                $innerBuilder->group(C::SCALE_NUM);
            }
            $innerBuilder->group('year(' . C::DATETIME_END . ')');
            $innerBuilder->group('dayofyear(' . C::DATETIME_END . ')');

            $this->builder->where(C::DATETIME_END, B::COMPARISON_IN, $innerBuilder);
        }
    }

    private function setOrder()
    {
        if (isResultTypeCargoList($this->resultType)) {
            $this->builder->order(C::CARGO_TYPE, false, 'latin1_bin');
        } elseif ($this->resultType == ResultType::COEFFS) {
            if ($this->filter->getScaleNum() == Constants::SCALE_NUM_ALL_TRAIN_SCALES) {
                $this->builder->order(C::SCALE_NUM);
            }
            $this->builder->order(C::DATETIME_END, true);
        } else {
            $orderColumn = C::DATETIME;

            $this->builder->order($orderColumn, true);

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

        $table = $this->getTableName();

        $this->builder
            ->params(B::SELECT_SQL_BUFFER_RESULT)
            ->table($table);

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

        $this->setGroup();

        $this->setOrder();
    }
}
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
     * @var ResultFilter
     */
    private $filter;

    /**
     * @param ResultFilter $filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

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
                    ->column(C::TARE)
                    ->column(C::NETTO);

                if ($this->scaleType == ScaleType::WMR) {
                    $this->builder
                        ->column(C::INVOICE_TARE)
                        ->column(C::INVOICE_NETTO)
                        ->column(C::INVOICE_OVERLOAD);
                }

                $this->builder->column(C::CARGO_TYPE);

                if ($this->resultType == ResultType::VAN_DYNAMIC_BRUTTO && $this->filter->isShowCargoDate()) {
                    $this->builder->column(C::DATETIME_CARGO);
                }

                $this->builder
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
        }
    }

    /**
     * @param string $table
     */
    private function setWhere($table)
    {
        // TODO: check and delete
//        switch ($table) {
//            case T::AUTO_BRUTTO:
//            case T::AUTO_TARE:
//            case T::KANAT:
//            case T::DP:
//                $dateTimeColumn = C::DATETIME;
//                break;
//
//            default:
//                $dateTimeColumn = C::UNIX_TIME;
//        }

        $dateTimeColumn = C::DATETIME;

        if ($this->filter->getScaleNum() != Constants::SCALE_NUM_ALL_TRAIN_SCALES) {
            $this->builder->where(C::SCALE_NUM, B::COMPARISON_EQUAL, $this->filter->getScaleNum());
        }

        $dateTimeStart = $this->filter->getDateTimeStart();
        $dateTimeEnd = $this->filter->getDateTimeEnd();

        if ($dateTimeColumn == C::DATETIME) {
            if ($dateTimeStart) {
                $dateTimeStart = (float)date(self::MYSQL_DATETIME_FORMAT, $dateTimeStart);
            }
            if ($dateTimeEnd) {
                $dateTimeEnd = (float)date(self::MYSQL_DATETIME_FORMAT, $dateTimeEnd);
            }
        }

        if ($dateTimeStart) {
            $this->builder
                ->where($dateTimeColumn, B::COMPARISON_GREATER_OR_EQUAL, $dateTimeStart);
        }
        if ($dateTimeEnd) {
            $this->builder
                ->where($dateTimeColumn, B::COMPARISON_LESS_OR_EQUAL, $dateTimeEnd);
        }

        $this->builder
            ->where($this->scaleType == ScaleType::AUTO ?
                C::AUTO_NUMBER :
                C::VAN_NUMBER, B::COMPARISON_LIKE,
                utf8ToLatin1($this->filter->getVanNumber()));

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
    }

    /**
     * @param string $table
     */
    private function setOrder($table)
    {
        if (isResultTypeCargoList($this->resultType)) {
            $this->builder->order(C::CARGO_TYPE, false, 'latin1_bin');
        } else {
            // TODO: check and delete
            switch ($table) {
                case T::AUTO_BRUTTO:
                case T::AUTO_TARE:
                case T::KANAT:
                case T::DP:
                    $orderColumn = C::DATETIME;
                    break;
                default:
                    $orderColumn = C::DATETIME;
//                    if ($this->filter->isOrderByDateTime()) {
//                        $orderColumn = C::DATETIME;
//                    } else {
//                        $orderColumn = C::UNIX_TIME;
//                    }
            }

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
        }
    }

    protected function makeQuery()
    {
        $table = $this->getTableName();

        $this->builder
            ->params(B::SELECT_SQL_BUFFER_RESULT)
            ->table($table);

        $this->setWhere($table);

        $this->setColumns();

        if ($this->resultType == ResultType::VAN_DYNAMIC_BRUTTO && $this->filter->isShowCargoDate()) {
            $this->builder->join(T::VAN_BRUTTO_ADD,
                array(
                    C::TRAIN_NUM,
                    C::SCALE_NUM,
                    C::SEQUENCE_NUMBER,
                    C::UNIX_TIME));
        }

        $this->setGroup();

        $this->setOrder($table);
    }
}
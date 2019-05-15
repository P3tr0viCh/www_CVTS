<?php
require_once "builders/query_builder/Builder.php";
require_once "Database.php";
require_once "QueryBase.php";

use QueryBuilder\Builder as B;
use Database\Tables as T;
use Database\Columns as C;

class QueryIron extends QueryBase
{
    const DATE_FORMAT = '%d.%m.%Y';

    const FUNCTION_DATE_FORMAT = 'date_format(%s, \'%s\')';
    const FUNCTION_STR_TO_DATE = 'str_to_date(%s, \'%s\')';
    const FUNCTION_IF = 'if(%s, %s, %s)';
    const FUNCTION_IFNULL = 'ifnull(%s, 0.0)';
    const FUNCTION_HOUR = 'hour(%s)';
    const FUNCTION_DATE_ADD = 'date_add(%s, INTERVAL 4 HOUR)';

    const SUB_QUERY = '(%s) %s %s';

    const MYSQL_DATE_START_FORMAT = "Ymd000000";
    const MYSQL_DATE_END_FORMAT = "Ymd235959";
    const MYSQL_DATE_START_FORMAT_20to20 = "Ymd200000";
    const MYSQL_DATE_END_FORMAT_20to20 = "Ymd195959";

    private $dateStart;
    private $dateEnd;
    private $orderByDesc;
    private $from20to20;

    /**
     * @param int $dateStart
     * @return $this
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;
        return $this;
    }

    /**
     * @param int $dateEnd
     * @return $this
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    /**
     * @param bool $orderByDesc
     * @return $this
     */
    public function setOrderByDesc($orderByDesc)
    {
        $this->orderByDesc = $orderByDesc;
        return $this;
    }

    /**
     * @param bool $from20to20
     * @return $this
     */
    public function setFrom20to20($from20to20)
    {
        $this->from20to20 = $from20to20;
        return $this;
    }

    /**
     * @throws Exception
     */
    protected function makeQuery()
    {
        $columnDate = $this->from20to20 ?
            sprintf(self::FUNCTION_IF, sprintf(self::FUNCTION_HOUR, C::DATETIME) . " >= 20",
                sprintf(self::FUNCTION_DATE_FORMAT,
                    sprintf(self::FUNCTION_DATE_ADD, C::DATETIME), self::DATE_FORMAT),
                sprintf(self::FUNCTION_DATE_FORMAT, C::DATETIME, self::DATE_FORMAT)) :
            sprintf(self::FUNCTION_DATE_FORMAT, C::DATETIME, self::DATE_FORMAT);

        $builder = B::getInstance()
            ->column($columnDate, null, C::IRON_DATE)
            ->where(C::CARGO_TYPE, B::COMPARISON_LIKE, utf8ToLatin1(CargoTypes::IRON))
            ->group(C::IRON_DATE);

        if ($this->dateStart) {
            if ($this->from20to20) {
                $this->dateStart = date_sub((new DateTime())->setTimestamp($this->dateStart), new DateInterval('P1D'))->getTimestamp();
            }

            $this->dateStart = (float)date(
                $this->from20to20 ?
                    self::MYSQL_DATE_START_FORMAT_20to20 :
                    self::MYSQL_DATE_START_FORMAT,
                $this->dateStart);
        }
        if ($this->dateEnd) {
            $this->dateEnd = (float)date($this->from20to20 ?
                self::MYSQL_DATE_END_FORMAT_20to20 :
                self::MYSQL_DATE_END_FORMAT,
                $this->dateEnd);
        }

        if ($this->dateStart) {
            $builder->where(C::DATETIME, B::COMPARISON_GREATER_OR_EQUAL, $this->dateStart);
        }
        if ($this->dateEnd) {
            $builder->where(C::DATETIME, B::COMPARISON_LESS_OR_EQUAL, $this->dateEnd);
        }

        $builderDyn = clone $builder;
        $builderDyn->table(T::VAN_DYNAMIC_BRUTTO);

        $builderSta = clone $builder;
        $builderSta->table(T::VAN_STATIC_BRUTTO);

        $builderEspc = clone $builderDyn;
        $builderEspc
            ->column(B::sum(C::NETTO), null, C::IRON_ESPC)
            ->where(C::SCALE_NUM, B::COMPARISON_IN, ScaleNums::IRON_ESPC);

        $builderRazl = clone $builderDyn;
        $builderRazl
            ->column(B::sum(C::NETTO), null, C::IRON_RAZL)
            ->where(C::SCALE_NUM, B::COMPARISON_IN, ScaleNums::IRON_RAZL);

        $builderShch = clone $builderSta;
        $builderShch
            ->column(B::sum(C::NETTO), null, C::IRON_SHCH)
            ->where(C::SCALE_NUM, B::COMPARISON_IN, ScaleNums::IRON_SHCH);

        $this->builder
            ->column(C::IRON_DATE)
            ->column(sprintf(self::FUNCTION_IFNULL, C::IRON_ESPC . ' + ' . C::IRON_RAZL), null, C::IRON_ESPC_RAZL)
            ->column(sprintf(self::FUNCTION_IFNULL, C::IRON_ESPC), null, C::IRON_ESPC)
            ->column(sprintf(self::FUNCTION_IFNULL, C::IRON_RAZL), null, C::IRON_RAZL)
            ->column(sprintf(self::FUNCTION_IFNULL, C::IRON_SHCH), null, C::IRON_SHCH)
            ->table(sprintf(self::SUB_QUERY, $builderEspc->build(), QueryBuilder\Expr::EXPR_AS, C::IRON_ESPC))
            ->join(sprintf(self::SUB_QUERY, $builderRazl->build(), QueryBuilder\Expr::EXPR_AS, C::IRON_RAZL), C::IRON_DATE)
            ->join(sprintf(self::SUB_QUERY, $builderShch->build(), QueryBuilder\Expr::EXPR_AS, C::IRON_SHCH), C::IRON_DATE)
            ->order(sprintf(self::FUNCTION_STR_TO_DATE, C::IRON_DATE, self::DATE_FORMAT), $this->orderByDesc);
    }
}
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
    const SUB_QUERY = '(%s) %s %s';

    const MYSQL_DATE_START_FORMAT = "Ymd000000";
    const MYSQL_DATE_END_FORMAT = "Ymd235959";

    const CARGO_TYPE_IRON = 'Чугун';
//    const CARGO_TYPE_IRON = 'CargoType 1%';

    const SCALE_NUM_ESPC = "10";
    const SCALE_NUM_RAZL = "182, 1043, 98";
    const SCALE_NUM_SHCH = "156, 31, 41";

    private $dateStart;
    private $dateEnd;
    private $orderByDesc;

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

    protected function makeQuery()
    {
        $builder = B::getInstance()
            ->column(sprintf(self::FUNCTION_DATE_FORMAT, C::DATETIME, self::DATE_FORMAT), null, C::IRON_DATE)
            ->where(C::CARGO_TYPE, B::COMPARISON_LIKE, utf8ToLatin1(self::CARGO_TYPE_IRON))
            ->group(C::IRON_DATE);

        if ($this->dateStart) {
            $this->dateStart = (float)date(self::MYSQL_DATE_START_FORMAT, $this->dateStart);
        }
        if ($this->dateEnd) {
            $this->dateEnd = (float)date(self::MYSQL_DATE_END_FORMAT, $this->dateEnd);
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
            ->where(C::SCALE_NUM, B::COMPARISON_IN, self::SCALE_NUM_ESPC);

        $builderRazl = clone $builderDyn;
        $builderRazl
            ->column(B::sum(C::NETTO), null, C::IRON_RAZL)
            ->where(C::SCALE_NUM, B::COMPARISON_IN, self::SCALE_NUM_RAZL);

        $builderShch = clone $builderSta;
        $builderShch
            ->column(B::sum(C::NETTO), null, C::IRON_SHCH)
            ->where(C::SCALE_NUM, B::COMPARISON_IN, self::SCALE_NUM_SHCH);

        $this->builder
            ->column(C::IRON_DATE)
            ->column(C::IRON_ESPC . ' + ' . C::IRON_RAZL, null, C::IRON_ESPC_RAZL)
            ->column(C::IRON_ESPC)
            ->column(C::IRON_RAZL)
            ->column(C::IRON_SHCH)
            ->table(sprintf(self::SUB_QUERY, $builderEspc->build(), QueryBuilder\Expr::EXPR_AS, C::IRON_ESPC))
            ->join(sprintf(self::SUB_QUERY, $builderRazl->build(), QueryBuilder\Expr::EXPR_AS, C::IRON_RAZL), C::IRON_DATE)
            ->join(sprintf(self::SUB_QUERY, $builderShch->build(), QueryBuilder\Expr::EXPR_AS, C::IRON_SHCH), C::IRON_DATE)
            ->order(sprintf(self::FUNCTION_STR_TO_DATE, C::IRON_DATE, self::DATE_FORMAT), $this->orderByDesc);
    }
}
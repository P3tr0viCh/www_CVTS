<?php
require_once "builders/query_builder/Builder.php";
require_once "Database.php";
require_once "QueryBase.php";

use QueryBuilder\Builder as B;
use Database\Tables as T;
use Database\Columns as C;

class QueryWAC extends QueryBase
{
    const COMPANY_DEPARTMENT = 0;

    // TODO: delete NOW()
    const SUB_QUERY = 'IFNULL((%s), NOW())';

    /**
     * @var int
     */
    private $department;

    /**
     * @param int $department
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    }

    protected function makeQuery()
    {
        $commonBuilder = B::getInstance()
            ->column(C::DATETIME)
            ->table(T::ACCIDENTS)
            ->order(C::DATETIME, true)
            ->limit(1);

        $companyBuilder = clone $commonBuilder;
        $companyBuilder->where(C::DEPARTMENT, B::COMPARISON_EQUAL, self::COMPANY_DEPARTMENT);

        $departmentBuilder = clone $commonBuilder;
        $departmentBuilder->where(C::DEPARTMENT, B::COMPARISON_EQUAL, $this->department);

        $this->builder
            ->column(sprintf(self::SUB_QUERY, $companyBuilder->build()), null, C::COMPANY_DATE)
            ->column(sprintf(self::SUB_QUERY, $departmentBuilder->build()), null, C::DEPARTMENT_DATE);
    }
}
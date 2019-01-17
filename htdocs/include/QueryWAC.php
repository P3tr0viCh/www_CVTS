<?php
require_once "builders/query_builder/Builder.php";
require_once "Database.php";
require_once "QueryBase.php";

use QueryBuilder\Builder as B;
use Database\Tables as T;
use Database\Columns as C;

class QueryWAC extends QueryBase
{
    const MAX_F = 'max(%s)';
    const SUB_QUERY_DATE = 'IFNULL((%s), NOW())';
    const SUB_QUERY_NAME = '(%s)';

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
        $companyDateBuilder = B::getInstance()
            ->column(sprintf(self::MAX_F, C::DATETIME))
            ->table(T::ACCIDENTS);

        $departmentDateBuilder = B::getInstance()
            ->column(C::DATETIME)
            ->table(T::ACCIDENTS)
            ->order(C::DATETIME, true)
            ->limit(1)
            ->where(C::DEPARTMENT, B::COMPARISON_EQUAL, $this->department);

        $departmentNameBuilder = B::getInstance()
            ->column(C::NAME)
            ->table(T::DEPARTMENTS)
            ->where(C::ID, B::COMPARISON_EQUAL, $this->department);

        $this->builder
            ->column(sprintf(self::SUB_QUERY_DATE, $companyDateBuilder->build()), null, C::COMPANY_DATE)
            ->column(sprintf(self::SUB_QUERY_DATE, $departmentDateBuilder->build()), null, C::DEPARTMENT_DATE)
            ->column(sprintf(self::SUB_QUERY_NAME, $departmentNameBuilder->build()), null, C::DEPARTMENT_NAME);
    }
}
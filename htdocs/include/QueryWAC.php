<?php
require_once "builders/query_builder/Builder.php";
require_once "QueryBase.php";

use builders\query_builder\Builder as B;
use builders\query_builder\Comparison;
use database\Tables as T;
use database\Columns as C;

class QueryWAC extends QueryBase
{
    const MAX_F = 'max(%s)';
    const SUB_QUERY_DATE = 'IFNULL((%s), NOW())';
    const SUB_QUERY_NAME = '(%s)';

    private int $department;

    public function setDepartment(int $department)
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
            ->where(C::DEPARTMENT, Comparison::EQUAL, $this->department);

        $departmentNameBuilder = B::getInstance()
            ->column(C::NAME)
            ->table(T::DEPARTMENTS)
            ->where(C::ID, Comparison::EQUAL, $this->department);

        $this->builder
            ->column(sprintf(self::SUB_QUERY_DATE, $companyDateBuilder->build()), null, C::COMPANY_DATE)
            ->column(sprintf(self::SUB_QUERY_DATE, $departmentDateBuilder->build()), null, C::DEPARTMENT_DATE)
            ->column(sprintf(self::SUB_QUERY_NAME, $departmentNameBuilder->build()), null, C::DEPARTMENT_NAME);
    }
}
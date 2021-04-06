<?php
require_once "builders/query_builder/Builder.php";
require_once "QueryBase.php";

abstract class QueryBaseDates extends QueryBase
{
    const MYSQL_DATETIME_FORMAT = "YmdHis";

    private ?int $dateTimeStart = null;
    private ?int $dateTimeEnd = null;

    public function getDateTimeStart(): ?int
    {
        return $this->dateTimeStart;
    }

    public function setDateTimeStart(?int $dateTimeStart): static
    {
        $this->dateTimeStart = $dateTimeStart ? (int)date(self::MYSQL_DATETIME_FORMAT, $dateTimeStart) : null;
        return $this;
    }

    public function getDateTimeEnd(): ?int
    {
        return $this->dateTimeEnd;
    }

    public function setDateTimeEnd(?int $dateTimeEnd): static
    {
        $this->dateTimeEnd = $dateTimeEnd ? (int)date(self::MYSQL_DATETIME_FORMAT, $dateTimeEnd) : null;
        return $this;
    }
}
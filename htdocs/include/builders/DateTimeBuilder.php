<?php

namespace builders;

use JetBrains\PhpStorm\Pure;

class DateTimeBuilder
{
    private ?int $day = null;
    private ?int $month = null;
    private ?int $year = null;
    private ?int $hour = null;
    private ?int $minute = null;

    #[Pure] public static function getInstance(): DateTimeBuilder
    {
        return new self();
    }

    public function setDay(?int $day): static
    {
        $this->day = $day;
        return $this;
    }

    public function setMonth(?int $month): static
    {
        $this->month = $month;
        return $this;
    }

    public function setYear(?int $year): static
    {
        $this->year = $year;
        return $this;
    }

    public function setHour(?int $hour): static
    {
        $this->hour = $hour;
        return $this;
    }

    public function setMinute(?int $minute): static
    {
        $this->minute = $minute;
        return $this;
    }

    public function buildStartDate(): ?int
    {
        $hasDate = $this->day || $this->month || $this->year || $this->hour || $this->minute;

        if ($hasDate) {
            $currDate = getdate();

            if ($this->year === null) $this->year = $currDate["year"];
            if ($this->month === null) $this->month = $currDate["mon"];
            if ($this->day === null) $this->day = $currDate["mday"];
            if ($this->hour === null) $this->hour = 0;
            if ($this->minute === null) $this->minute = 0;

            $result = mktime($this->hour, $this->minute, 0, $this->month, $this->day, $this->year);

            return $result === false ? null : $result;
        }

        return null;
    }

    public function buildEndDate(): ?int
    {
        $hasDate = $this->day || $this->month || $this->year || $this->hour || $this->minute;

        if ($hasDate) {
            $currDate = getdate();

            if ($this->year === null) $this->year = $currDate["year"];
            if ($this->month === null) $this->month = $currDate["mon"];
            if ($this->day === null) $this->day = getLastDay($this->month, $this->year);
            if ($this->hour === null) $this->hour = 23;
            if ($this->minute === null) $this->minute = 59;

            $result = mktime($this->hour, $this->minute, 59, $this->month, $this->day, $this->year);

            return $result === false ? null : $result;
        }

        return null;
    }
}
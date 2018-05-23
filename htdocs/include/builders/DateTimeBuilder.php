<?php

class DateTimeBuilder
{
    private $day;
    private $month;
    private $year;
    private $hour;
    private $minute;

    /**
     * @return DateTimeBuilder
     */
    public static function getInstance()
    {
        return new self();
    }

    /**
     * @param int $day
     * @return DateTimeBuilder
     */
    public function setDay($day)
    {
        $this->day = $day;
        return $this;
    }

    /**
     * @param int $month
     * @return DateTimeBuilder
     */
    public function setMonth($month)
    {
        $this->month = $month;
        return $this;
    }

    /**
     * @param int $year
     * @return DateTimeBuilder
     */
    public function setYear($year)
    {
        $this->year = $year;
        return $this;
    }

    /**
     * @param int $hour
     * @return DateTimeBuilder
     */
    public function setHour($hour)
    {
        $this->hour = $hour;
        return $this;
    }

    /**
     * @param int $minute
     * @return DateTimeBuilder
     */
    public function setMinute($minute)
    {
        $this->minute = $minute;
        return $this;
    }

    /**
     * @return int|null
     */
    public function buildStartDate()
    {
        $hasDate = $this->day || $this->month || $this->year || $this->hour || $this->minute;
        if ($hasDate) {
            $currDate = getdate();

            if ($this->hour === null) $this->hour = 0;
            if ($this->minute === null) $this->minute = 0;
            if ($this->day === null) $this->day = $currDate["mday"];
            if ($this->month === null) $this->month = $currDate["mon"];
            if ($this->year === null) $this->year = $currDate["year"];

            $result = mktime($this->hour, $this->minute, 0, $this->month, $this->day, $this->year);

            return $result === false ? null : $result;
        } else {
            return null;
        }
    }

    /**
     * @return int|null
     */
    public function buildEndDate()
    {
        $hasDate = $this->day || $this->month || $this->year || $this->hour || $this->minute;
        if ($hasDate) {
            $currDate = getdate();

            if ($this->hour === null) $this->hour = 23;
            if ($this->minute === null) $this->minute = 59;
            if ($this->day === null) $this->day = getLastDay($this->month, $this->year);
            if ($this->month === null) $this->month = $currDate["mon"];
            if ($this->year === null) $this->year = $currDate["year"];

            $result = mktime($this->hour, $this->minute, 59, $this->month, $this->day, $this->year);

            return $result === false ? null : $result;
        } else {
            return null;
        }
    }
}
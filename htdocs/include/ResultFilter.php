<?php
require_once "Functions.php";

class ResultFilter
{
    private $scaleNum;
    private $trainNum;

    private $dateTimeStart;
    private $dateTimeEnd;

    private $trainUnixTime;
    private $trainDateTime;

    private $vanNumber;
    private $cargoType;
    private $invoiceNum;
    private $invoiceSupplier;
    private $invoiceRecipient;
    private $onlyChark;
    private $scalesFilter;

    private $full;
    private $showCargoDate;
    private $orderByDateTime;
    private $compareForward;
    private $compareByBrutto;

    /**
     * @return ResultFilter
     */
    public static function getInstance()
    {
        return new self();
    }

    /**
     * @return int|null
     */
    public function getDateTimeStart()
    {
        return $this->dateTimeStart;
    }

    /**
     * @param int $dateTimeStart
     * @return $this
     */
    public function setDateTimeStart($dateTimeStart)
    {
        $this->dateTimeStart = $dateTimeStart;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDateTimeEnd()
    {
        return $this->dateTimeEnd;
    }

    /**
     * @param int $dateTimeEnd
     * @return $this
     */
    public function setDateTimeEnd($dateTimeEnd)
    {
        $this->dateTimeEnd = $dateTimeEnd;
        return $this;
    }

    /**
     * Для доменных печей: выводить только "Кокс".
     *
     * @param bool $onlyChark
     * @return $this
     */
    public function setOnlyChark($onlyChark)
    {
        $this->onlyChark = $onlyChark;
        return $this;
    }

    /**
     * Сортировать по полю bdatetime (иначе по wtime).
     *
     * @param bool $orderByDateTime
     * @return $this
     */
    public function setOrderByDateTime($orderByDateTime)
    {
        $this->orderByDateTime = $orderByDateTime;
        return $this;
    }

    /**
     * Выводить время изменения рода груза.
     *
     * @param bool $showCargoDate
     * @return $this
     */
    public function setShowCargoDate($showCargoDate)
    {
        $this->showCargoDate = $showCargoDate;
        return $this;
    }

    /**
     * Показывать все поля таблицы.
     *
     * @param bool $full
     * @return $this
     */
    public function setFull($full)
    {
        $this->full = $full;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getScaleNum()
    {
        return $this->scaleNum;
    }

    /**
     * @param int $scaleNum
     * @return $this
     */
    public function setScaleNum($scaleNum)
    {
        $this->scaleNum = (int)$scaleNum;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrainNum()
    {
        return (int)$this->trainNum;
    }

    /**
     * @param int $trainNum
     * @return $this
     */
    public function setTrainNum($trainNum)
    {
        $this->trainNum = $trainNum;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVanNumber()
    {
        return $this->vanNumber;
    }

    /**
     * Номер вагона или автомобиля.
     *
     * @param string $vanNumber
     * @return $this
     */
    public function setVanNumber($vanNumber)
    {
        $this->vanNumber = $vanNumber;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCargoType()
    {
        return $this->cargoType;
    }

    /**
     * Род груза.
     *
     * @param string $cargoType
     * @return $this
     */
    public function setCargoType($cargoType)
    {
        $this->cargoType = $cargoType;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getInvoiceNum()
    {
        return $this->invoiceNum;
    }

    /**
     * Номер накладной.
     *
     * @param string $invoiceNum
     * @return $this
     */
    public function setInvoiceNum($invoiceNum)
    {
        $this->invoiceNum = $invoiceNum;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getInvoiceSupplier()
    {
        return $this->invoiceSupplier;
    }

    /**
     * Грузополучатель.
     *
     * @param string $invoiceSupplier
     * @return $this
     */
    public function setInvoiceSupplier($invoiceSupplier)
    {
        $this->invoiceSupplier = $invoiceSupplier;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getInvoiceRecipient()
    {
        return $this->invoiceRecipient;
    }

    /**
     * Грузоотправитель.
     *
     * @param string $invoiceRecipient
     * @return $this
     */
    public function setInvoiceRecipient($invoiceRecipient)
    {
        $this->invoiceRecipient = $invoiceRecipient;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function isOnlyChark()
    {
        return $this->onlyChark;
    }

    /**
     * @return string|null
     */
    public function getScalesFilter()
    {
        return $this->scalesFilter;
    }

    /**
     * Номера весов.
     *
     * @param string $scalesFilter
     * @return $this
     */
    public function setScalesFilter($scalesFilter)
    {
        $this->scalesFilter = $scalesFilter;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFull()
    {
        return (bool)$this->full;
    }

    /**
     * @return bool|null
     */
    public function isShowCargoDate()
    {
        return $this->showCargoDate;
    }

    /**
     * @return bool|null
     */
    public function isOrderByDateTime()
    {
        return $this->orderByDateTime;
    }

    /**
     * @return bool|null
     */
    public function isCompareForward()
    {
        return $this->compareForward;
    }

    /**
     * @param bool $compareForward
     * @return $this
     */
    public function setCompareForward($compareForward)
    {
        $this->compareForward = (bool)$compareForward;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function isCompareByBrutto()
    {
        return $this->compareByBrutto;
    }

    /**
     * @param bool $compareByBrutto
     * @return $this
     */
    public function setCompareByBrutto($compareByBrutto)
    {
        $this->compareByBrutto = (bool)$compareByBrutto;
        return $this;
    }

    /**
     * @return int
     */
    public function getTrainUnixTime()
    {
        return $this->trainUnixTime;
    }

    /**
     * @param int $trainUnixTime
     * @return $this
     */
    public function setTrainUnixTime($trainUnixTime)
    {
        $this->trainUnixTime = $trainUnixTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTrainDateTime()
    {
        return $this->trainDateTime;
    }

    /**
     * @param mixed $trainDateTime
     * @return $this
     */
    public function setTrainDateTime($trainDateTime)
    {
        $this->trainDateTime = $trainDateTime;
        return $this;
    }
}
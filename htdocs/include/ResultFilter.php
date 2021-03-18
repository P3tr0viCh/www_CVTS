<?php
require_once "Functions.php";

class ResultFilter
{
    private ?int $scaleNum = null;
    private ?int $trainNum = null;

    private ?int $dateTimeStart = null;
    private ?int $dateTimeEnd = null;

    private ?int $trainUnixTime = null;
    private ?int $trainDateTime = null;

    private ?string $vanNumber = null;
    private ?string $cargoType = null;
    private ?string $invoiceNum = null;
    private ?string $invoiceSupplier = null;
    private ?string $invoiceRecipient = null;
    private ?bool $onlyChark = null;
    private ?string $scalesFilter = null;

    private ?bool $full = null;
    private ?bool $showCargoDate = null;
    private ?bool $showDeltas = null;
    private ?bool $showDeltasMi3115 = null;
    private ?bool $compareForward = null;
    private ?bool $compareByBrutto = null;

    public function setScaleNum(?int $scaleNum): static
    {
        $this->scaleNum = (int)$scaleNum;
        return $this;
    }

    public function getScaleNum(): ?int
    {
        return $this->scaleNum;
    }

    public function setTrainNum(?int $trainNum): static
    {
        $this->trainNum = $trainNum;
        return $this;
    }

    public function getTrainNum(): ?int
    {
        return (int)$this->trainNum;
    }

    public function setDateTimeStart(?int $dateTimeStart): static
    {
        $this->dateTimeStart = $dateTimeStart;
        return $this;
    }

    public function getDateTimeStart(): ?int
    {
        return $this->dateTimeStart;
    }

    public function setDateTimeEnd(?int $dateTimeEnd): static
    {
        $this->dateTimeEnd = $dateTimeEnd;
        return $this;
    }

    public function getDateTimeEnd(): ?int
    {
        return $this->dateTimeEnd;
    }

    public function setTrainUnixTime(?int $trainUnixTime): static
    {
        $this->trainUnixTime = $trainUnixTime;
        return $this;
    }

    public function getTrainUnixTime(): ?int
    {
        return $this->trainUnixTime;
    }

    public function setTrainDateTime(?int $trainDateTime): static
    {
        $this->trainDateTime = $trainDateTime;
        return $this;
    }

    public function getTrainDateTime(): ?int
    {
        return $this->trainDateTime;
    }

    public function setVanNumber(?string $vanNumber): static
    {
        $this->vanNumber = $vanNumber;
        return $this;
    }

    public function getVanNumber(): ?string
    {
        return $this->vanNumber;
    }

    public function setCargoType(?string $cargoType): static
    {
        $this->cargoType = $cargoType;
        return $this;
    }

    public function getCargoType(): ?string
    {
        return $this->cargoType;
    }

    public function setInvoiceNum(?string $invoiceNum): static
    {
        $this->invoiceNum = $invoiceNum;
        return $this;
    }

    public function getInvoiceNum(): ?string
    {
        return $this->invoiceNum;
    }

    /**
     * Грузоотправитель.
     *
     * @param string|null $invoiceSupplier
     * @return $this
     */
    public function setInvoiceSupplier(?string $invoiceSupplier): static
    {
        $this->invoiceSupplier = $invoiceSupplier;
        return $this;
    }

    public function getInvoiceSupplier(): ?string
    {
        return $this->invoiceSupplier;
    }

    /**
     * Грузополучатель.
     *
     * @param string|null $invoiceRecipient
     * @return $this
     */
    public function setInvoiceRecipient(?string $invoiceRecipient): static
    {
        $this->invoiceRecipient = $invoiceRecipient;
        return $this;
    }

    public function getInvoiceRecipient(): ?string
    {
        return $this->invoiceRecipient;
    }

    /**
     * Для доменных печей: выводить только "Кокс".
     *
     * @param bool|null $onlyChark
     * @return ResultFilter
     */
    public function setOnlyChark(?bool $onlyChark): static
    {
        $this->onlyChark = $onlyChark;
        return $this;
    }

    public function isOnlyChark(): ?bool
    {
        return $this->onlyChark;
    }

    /**
     * Номера весов.
     *
     * @param string|null $scalesFilter
     * @return ResultFilter
     */
    public function setScalesFilter(?string $scalesFilter): static
    {
        $this->scalesFilter = $scalesFilter;
        return $this;
    }

    public function getScalesFilter(): ?string
    {
        return $this->scalesFilter;
    }

    /**
     * Показывать все поля таблицы.
     *
     * @param bool|null $full
     * @return ResultFilter
     */
    public function setFull(?bool $full): static
    {
        $this->full = $full;
        return $this;
    }

    public function isFull(): ?bool
    {
        return $this->full;
    }

    /**
     * Выводить время изменения рода груза.
     *
     * @param bool|null $showCargoDate
     * @return ResultFilter
     */
    public function setShowCargoDate(?bool $showCargoDate): static
    {
        $this->showCargoDate = $showCargoDate;
        return $this;
    }

    public function isShowCargoDate(): ?bool
    {
        return $this->showCargoDate;
    }

    /**
     * Выводить предельно допускаемые погрешности.
     *
     * @param bool|null $showDeltas
     * @return ResultFilter
     */
    public function setShowDeltas(?bool $showDeltas): static
    {
        $this->showDeltas = $showDeltas;
        return $this;
    }

    public function isShowDeltas(): ?bool
    {
        return $this->showDeltas;
    }

    /**
     * Выводить редельные расхождения по МИ 3115.
     *
     * @param bool|null $showDeltasMi3115
     * @return ResultFilter
     */
    public function setShowDeltasMi3115(?bool $showDeltasMi3115): static
    {
        $this->showDeltasMi3115 = $showDeltasMi3115;
        return $this;
    }

    public function isShowDeltasMi3115(): ?bool
    {
        return $this->showDeltasMi3115;
    }

    public function setCompareForward(?bool $compareForward): static
    {
        $this->compareForward = $compareForward;
        return $this;
    }

    public function isCompareForward(): ?bool
    {
        return $this->compareForward;
    }

    public function setCompareByBrutto(?bool $compareByBrutto): static
    {
        $this->compareByBrutto = $compareByBrutto;
        return $this;
    }

    public function isCompareByBrutto(): ?bool
    {
        return $this->compareByBrutto;
    }
}
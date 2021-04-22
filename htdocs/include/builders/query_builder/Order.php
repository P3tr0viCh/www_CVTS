<?php

namespace builders\query_builder;

class Order
{
    private bool $order;
    private ?string $collate;

    public function __construct(bool $order, ?string $collate = null)
    {
        $this->order = $order;
        $this->collate = $collate;
    }

    public function isDesc(): bool
    {
        return $this->order;
    }

    public function getCollate(): ?string
    {
        return $this->collate;
    }
}
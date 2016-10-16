<?php

namespace QueryBuilder;

class Order
{
    /**
     * @var bool
     */
    private $order;
    /**
     * @var null|string
     */
    private $collate;

    /**
     * Order constructor.
     * @param bool $order
     * @param null|string $collate
     */
    public function __construct($order, $collate = null)
    {
        $this->order = $order;
        $this->collate = $collate;
    }

    /**
     * @return boolean
     */
    public function isDesc()
    {
        return $this->order;
    }

    /**
     * @return null|string
     */
    public function getCollate()
    {
        return $this->collate;
    }
}
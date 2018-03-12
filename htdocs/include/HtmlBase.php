<?php

abstract class HtmlBase
{
    protected $newDesign;

    /**
     * HtmlBase constructor.
     * @param bool $newDesign
     */
    public function __construct($newDesign)
    {
        $this->newDesign = $newDesign;
    }

    protected abstract function drawNewDesign();

    protected abstract function drawCompat();

    public function draw()
    {
        if ($this->newDesign) {
            $this->drawNewDesign();
        } else {
            $this->drawCompat();
        }
    }
}
<?php

abstract class HtmlBase
{
    protected bool $newDesign;

    public function __construct(bool $newDesign)
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
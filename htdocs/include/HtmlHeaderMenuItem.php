<?php

class HtmlHeaderMenuItem
{
    private string $id;
    private string $text;
    private string $onClick;

    public function __construct(string $id, string $text, string $onClick)
    {
        $this->id = $id;
        $this->text = $text;
        $this->onClick = $onClick;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getOnClick(): string
    {
        return $this->onClick;
    }
}
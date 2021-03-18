<?php

class HtmlHeaderNavLink
{
    private string $id;
    private string $icon;
    private string $text;
    private string $onClick;
    private bool $hidden;

    public function __construct(string $id, string $icon, string $text, string $onClick, bool $hidden = false)
    {
        $this->id = $id;
        $this->icon = $icon;
        $this->text = $text;
        $this->onClick = $onClick;
        $this->hidden = $hidden;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getHidden(): bool
    {
        return $this->hidden;
    }

    public function getIcon(): string
    {
        return $this->icon;
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
<?php

class HtmlHeaderNavLink
{
    private $id;
    private $icon;
    private $text;
    private $onClick;
    private $hidden;

    /**
     * HtmlHeaderNavLink constructor.
     * @param string $id
     * @param string $icon
     * @param string $text
     * @param string $onClick
     * @param bool $hidden
     */
    public function __construct($id, $icon, $text, $onClick, $hidden = false)
    {
        $this->id = $id;
        $this->icon = $icon;
        $this->text = $text;
        $this->onClick = $onClick;
        $this->hidden = $hidden;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getOnClick()
    {
        return $this->onClick;
    }
}
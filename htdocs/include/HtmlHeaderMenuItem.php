<?php

class HtmlHeaderMenuItem
{
    private $id;
    private $text;
    private $onClick;

    /**
     * HtmlHeaderMenuItem constructor.
     * @param string $id
     * @param string $text
     * @param string $onClick
     */
    public function __construct($id, $text, $onClick)
    {
        $this->id = $id;
        $this->text = $text;
        $this->onClick = $onClick;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
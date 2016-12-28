<?php

namespace HrefBuilder;

class Builder
{
    /**
     * @var string
     */
    private $url;
    /**
     * @var array
     */
    private $params;

    /**
     * Создание нового экземпляра Builder.
     *
     * @return Builder
     */
    public static function getInstance()
    {
        return new self;
    }

    /**
     * Очистка.
     *
     * @return $this
     */
    public function clear()
    {
        $this->url = null;
        $this->params = null;
        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    function setParam($name, $value)
    {
        if (isset($name) && is_string($name) && $name != "") {
            $this->params[$name] = $value;
        }
        return $this;
    }

    /**
     * @return string
     */
    function build()
    {
        $query = null;

        if ($this->params) {
            $params = array();

            foreach ($this->params as $param => $value) {
                if (is_bool($value)) {
                    $value = $value ? "true" : "false";
                } elseif (is_string($value)) {
                    $value = urlencode($value);
                }

                $params[] = $param . "=" . $value;
            }

            $query = implode("&", $params);
        }

        if ($this->url && $query) {
            return $this->url . "?" . $query;
        } elseif ($this->url) {
            return $this->url;
        } elseif ($query) {
            return $query;
        } else {
            return "";
        }
    }
}
<?php

namespace builders\href_builder;

use JetBrains\PhpStorm\Pure;

class Builder
{
    private ?string $url = null;
    private ?array $params = null;

    #[Pure] public static function getInstance(): Builder
    {
        return new self;
    }

    public function clear(): static
    {
        $this->url = null;
        $this->params = null;
        return $this;
    }

    function setUrl(?string $url): static
    {
        $this->url = $url;
        return $this;
    }

    function setParam(?string $name, mixed $value): static
    {
        if (isset($name) && !empty($name) && is_string($name)) {
            $this->params[$name] = $value;
        }
        return $this;
    }

    function build(): ?string
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
            return null;
        }
    }
}
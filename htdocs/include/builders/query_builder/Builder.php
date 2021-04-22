<?php

namespace builders\query_builder;

use JetBrains\PhpStorm\Pure;

require_once "Expr.php";
require_once "Join.php";
require_once "Where.php";
require_once "Order.php";
require_once "Comparison.php";

/**
 * Строитель запроса SELECT.
 *
 * Использование: см. BuilderTest.php
 *
 * @package query_builder
 */
class Builder
{
    /**
     * Параметр запроса SQL_BUFFER_RESULT;
     */
    const SELECT_SQL_BUFFER_RESULT = 1;

    private ?int $params = null;

    /**
     * @var string|null[]
     */
    private ?array $columns = null;

    /**
     * @var string|null[]
     */
    private ?array $table = null;

    /**
     * @var null|Join[]
     */
    private ?array $join = null;

    /**
     * @var null|Where[]
     */
    private ?array $where = null;

    /**
     * @var string|null[]
     */
    private ?array $group = null;

    /**
     * @var null|Order[]
     */
    private ?array $order = null;

    private ?int $limit = null;

    private static function concat(?string $s1, ?string $s2, ?string $separator): ?string
    {
        $s1NotEmpty = !empty($s1);
        $s2NotEmpty = !empty($s2);

        if ($s1NotEmpty && $s2NotEmpty) {
            return $s1 . $separator . $s2;
        } elseif ($s1NotEmpty) {
            return $s1;
        } elseif ($s2NotEmpty) {
            return $s2;
        } else {
            return null;
        }
    }

    private static function format(mixed $value): string
    {
        if ($value === null) {
            return "''";
        }
        if (is_string($value)) {
            $value = str_replace(
                array("\\", "\x00", "\n", "\r", "'", '"', "\x1a"),
                array("\\\\", "\\0", "\\n", "\\r", "\'", '\"', "\\Z"), $value);

            return "'" . $value . "'";
        }
        if (is_bool($value)) {
            return $value ? "TRUE" : "FALSE";
        }
        return (string)$value;
    }

    public static function sum(string $column): string
    {
        return "sum(" . $column . ")";
    }

    public static function max(string $column): string
    {
        return "max(" . $column . ")";
    }

    #[Pure] private function getParams(): ?string
    {
        $result = null;

        if ($this->params & self::SELECT_SQL_BUFFER_RESULT) {
            $result = self::concat($result, Expr::SQL_BUFFER_RESULT, Expr::SPACE);
        }

        return $result;
    }

    #[Pure] private function getColumns(): ?string
    {
        $result = null;

        if ($this->columns) {
            foreach ($this->columns as $column) {
                $result = self::concat($result, $column, Expr::COMMA . Expr::SPACE);
            }
        }

        return $result != null ? $result : Expr::ALL;
    }

    #[Pure] private function getTable(): ?string
    {
        $result = null;

        if ($this->table) {
            foreach ($this->table as $table) {
                $result = self::concat($result, $table, Expr::COMMA . Expr::SPACE);
            }
        }

        return $result != null ? Expr::FROM . Expr::SPACE . $result : null;
    }

    #[Pure] private function getJoin(): ?string
    {
        $result = null;

        if ($this->join) {
            foreach ($this->join as $join) {
                $table = $join->getTable();
                $columnsArray = $join->getColumns();
                $columns = null;

                foreach ($columnsArray as $column) {
                    $columns = self::concat($columns, $column, Expr::COMMA . Expr::SPACE);
                }

                $result = self::concat($result, sprintf(Expr::JOIN, $table, $columns), Expr::SPACE);
            }
        }

        return $result;
    }

    private function getWhere(): ?string
    {
        $result = null;

        if ($this->where) {
            foreach ($this->where as $where) {
                $column = $where->getColumn();
                $comparison = $where->getComparison();
                $value = $where->getValue();
                if ($value === null) {
                    $value = "";
                }

                if ($comparison == Comparison::LIKE) {
                    $expr = sprintf(Expr::LIKE, Builder::format((string)$value));
                } elseif ($comparison == Comparison::IN) {
                    if (($value instanceof Builder)) {
                        $expr = sprintf(Expr::IN, $value->build());
                    } else {
                        $valuesArray = explode(Expr::COMMA, (string)$value);

                        $values = null;
                        foreach ($valuesArray as $value) {
                            $value = trim($value);

                            if (floatval($value)) {
                                $value = Builder::format((float)$value);
                            } elseif (intval($value)) {
                                $value = Builder::format((int)$value);
                            } else {
                                $l = strlen($value);
                                if (($l > 1) && ($value[0] == "'" || $value[0] == '"') && ($value[0] == $value[$l - 1])) {
                                    $value = substr($value, 1, $l - 2);
                                }
                                $value = Builder::format($value);
                            }

                            $values = self::concat($values, $value, Expr::COMMA . Expr::SPACE);
                        }

                        $expr = sprintf(Expr::IN, $values);
                    }
                } else {
                    $expr = match ($comparison) {
                        Comparison::NOT_EQUAL => Expr::NOT_EQUAL,
                        Comparison::LESS => Expr::LESS,
                        Comparison::LESS_OR_EQUAL => Expr::LESS_OR_EQUAL,
                        Comparison::GREATER => Expr::GREATER,
                        Comparison::GREATER_OR_EQUAL => Expr::GREATER_OR_EQUAL,
                        default => Expr::EQUAL,
                    };

                    $value = Builder::format($value);

                    $expr = $expr . Expr::SPACE . $value;
                }

                $expr = $column . Expr::SPACE . $expr;

                $result = self::concat($result, $expr, Expr::SPACE . Expr::EXPR_AND . Expr::SPACE);
            }
        }

        return $result != null ? Expr::WHERE . Expr::SPACE . $result : null;
    }

    #[Pure] private function getGroup(): ?string
    {
        $result = null;

        if ($this->group) {
            foreach ($this->group as $column) {
                $result = self::concat($result, $column, Expr::COMMA . Expr::SPACE);
            }
        }

        return $result != null ? Expr::GROUP . Expr::SPACE . $result : null;
    }

    #[Pure] private function getOrder(): ?string
    {
        $result = null;

        if ($this->order) {
            foreach ($this->order as $column => $order) {
                $collate = $order->getCollate();
                if (!empty($collate)) {
                    $column = $column . Expr::SPACE . Expr::COLLATE . Expr::SPACE . $collate;
                }
                if ($order->isDesc()) {
                    $column = $column . Expr::SPACE . Expr::DESC;
                }
                $result = self::concat($result, $column, Expr::COMMA . Expr::SPACE);
            }
        }

        return $result != null ? Expr::ORDER . Expr::SPACE . $result : null;
    }

    #[Pure] private function getLimit(): ?string
    {
        return isset($this->limit) ? Expr::LIMIT . Expr::SPACE . $this->limit : null;
    }

    /**
     * Создание нового экземпляра Builder.
     *
     * @return Builder
     */
    #[Pure] public static function getInstance(): Builder
    {
        return new self;
    }

    /**
     * Очистка.
     *
     * @return $this
     */
    public function clear(): static
    {
        $this->params = null;
        $this->columns = null;
        $this->table = null;
        $this->join = null;
        $this->where = null;
        $this->group = null;
        $this->order = null;
        $this->limit = null;
        return $this;
    }

    /**
     * Параметры запроса.
     * SQL_BUFFER_RESULT.
     *
     * @param int $params
     * @return $this
     * @see Builder::SELECT_SQL_BUFFER_RESULT
     */
    public function params(int $params): static
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Добавление колонки в запрос.
     * <p>
     * Если в построитель не добавлена ни одна колонка,
     * запрос строится с выводом всех колонок (SELECT *).
     * <p>
     * имя_колонки
     * <p>
     * имя_таблицы.имя_колонки
     * <p>
     * имя_колонки AS псевдоним
     *
     * @param string|null $column Имя колонки.
     * @param string|null $table Имя таблицы, которой принадлежит колонка.
     * @param string|null $alias Псевдоним для колонки.
     *
     * @return $this
     */
    public function column(?string $column, ?string $table = null, ?string $alias = null): static
    {
        if (!empty($column)) {
            if (!empty($table)) {
                $column = $table . Expr::DOT . $column;
            }
            if (!empty($alias)) {
                $column = $column . Expr::SPACE . Expr::EXPR_AS . Expr::SPACE . $alias;
            }

            $this->columns[] = $column;
        }
        return $this;
    }

    /**
     * Добавление таблицы в запрос.
     * <p>
     * FROM имя_таблицы. Поддерживается несколько таблиц в запросе.
     *
     * @param string|null $table
     * @param string|null $alias Псевдоним для таблицы.
     *
     * @return $this
     */
    public function table(?string $table, ?string $alias = null): static
    {
        if (!empty($table)) {
            if (!empty($alias)) {
                $table = $table . Expr::SPACE . Expr::EXPR_AS . Expr::SPACE . $alias;
            }

            $this->table[] = $table;
        }
        return $this;
    }

    /**
     * Добавление объединения  в запрос.
     * <p>
     * LEFT JOIN имя_таблицы USING (имя_колонки1, имя_колонки2...).
     *
     * @param string|null $table Имя таблицы.
     * @param array|string $columns Имя колонки (string) или имена колонок (array).
     * @param string|null $alias Псевдоним для таблицы.
     *
     * @return $this
     */
    public function join(?string $table, array|string $columns, ?string $alias = null): static
    {
        if (!empty($table)) {
            if (!is_array($columns)) {
                $columns = array($columns);
            }
            if (!empty($alias)) {
                $table = $table . Expr::SPACE . Expr::EXPR_AS . Expr::SPACE . $alias;
            }

            $this->join[] = new Join($table, $columns);
        }

        return $this;
    }

    /**
     * Добавление условия выборки в запрос.
     * <p>
     * WHERE условие_1 AND условие_2 AND ...
     * <p>
     * Поддерживается только AND.
     *
     * @param string|null $column Имя колонки.
     * @param int $comparison Сравнение.
     * @param mixed $value Значение.
     *
     * @return $this
     *
     * @see Comparison::EQUAL
     * @see Comparison::NOT_EQUAL
     * @see Comparison::LESS
     * @see Comparison::LESS_OR_EQUAL
     * @see Comparison::GREATER
     * @see Comparison::GREATER_OR_EQUAL
     * @see Comparison::LIKE
     * @see Comparison::IN
     */
    public function where(?string $column, int $comparison, mixed $value): static
    {
        if (!empty($column) && $value !== null) {
            $this->where[] = new Where($column, $comparison, $value);
        }
        return $this;
    }

    /**
     * Добавление в запрос условия группировки.
     * <p>
     * GROUP BY имя_колонки_1, имя_колонки_2...
     *
     * @param string|null $column Имя колонки.
     *
     * @return $this
     */
    public function group(?string $column): static
    {
        if (!empty($column)) {
            $this->group[] = $column;
        }
        return $this;
    }

    /**
     * Добавление в запрос сортировки.
     * <p>
     * ORDER BY имя_колонки_1, имя_колонки_2 DESC, имя_колонки_3 COLLATE latin1_bin.
     *
     * @param string|null $column Имя колонки
     * @param bool $desc Направление сортировки (ASC|DESC).
     * @param string|null $collate Кодировка колонки.
     *
     * @return $this
     */
    public function order(?string $column, bool $desc = false, ?string $collate = null): static
    {
        if (!empty($column)) {
            $this->order[$column] = new Order($desc, $collate);
        }
        return $this;
    }

    /**
     * Добавление в запрос лимита.
     * <p>
     * Поддерживается только LIMIT число_строк.
     *
     * @param int|null $count
     *
     * @return $this
     */
    public function limit(?int $count): static
    {
        $this->limit = isset($count) && $count > 0 ? $count : null;
        return $this;
    }

    /**
     * Возвращает построенный запрос.
     *
     * @return string|null
     */
    public function build(): ?string
    {
        $result = self::concat(Expr::SELECT, $this->getParams(), Expr::SPACE);
        $result = self::concat($result, $this->getColumns(), Expr::SPACE);
        $result = self::concat($result, $this->getTable(), Expr::SPACE);
        $result = self::concat($result, $this->getJoin(), Expr::SPACE);
        $result = self::concat($result, $this->getWhere(), Expr::SPACE);
        $result = self::concat($result, $this->getGroup(), Expr::SPACE);
        $result = self::concat($result, $this->getOrder(), Expr::SPACE);
        return self::concat($result, $this->getLimit(), Expr::SPACE);
    }
}
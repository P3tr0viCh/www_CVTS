<?php

namespace QueryBuilder;

require_once "Expr.php";
require_once "Join.php";
require_once "Where.php";
require_once "Order.php";

/**
 * Строитель запроса SELECT.
 *
 * Использование: см. BuilderTest.php
 *
 * @package QueryBuilder
 */
class Builder
{
    /**
     * Сравнение WHERE "равно (=)".
     */
    const COMPARISON_EQUAL = 0;

    /**
     * Сравнение WHERE "не равно (<>)".
     */
    const COMPARISON_NOT_EQUAL = 1;

    /**
     * Сравнение WHERE "меньше (<)".
     */
    const COMPARISON_LESS = 2;

    /**
     * Сравнение WHERE "меньше или равно (<=)".
     */
    const COMPARISON_LESS_OR_EQUAL = 3;

    /**
     * Сравнение WHERE "больше (>)".
     */
    const COMPARISON_GREATER = 4;

    /**
     * Сравнение WHERE " больше или равно (>=)".
     */
    const COMPARISON_GREATER_OR_EQUAL = 5;

    /**
     * Сравнение WHERE "LIKE 'значение'".
     */
    const COMPARISON_LIKE = 6;

    /**
     * Сравнение WHERE "IN (значение_1, значение_2)".
     */
    const COMPARISON_IN = 7;

    /**
     * Параметр запроса SQL_BUFFER_RESULT;
     */
    const SELECT_SQL_BUFFER_RESULT = 1;

    /**
     * @var null|int
     */
    private $params;

    /**
     * @var null|string[]
     */
    private $columns;

    /**
     * @var null|string
     */
    private $table;

    /**
     * @var null|Join
     */
    private $join;

    /**
     * @var null|Where[]
     */
    private $where;

    /**
     * @var null|string[string]
     */
    private $group;

    /**
     * @var null|Order[string]
     */
    private $order;

    /**
     * @var null|int
     */
    private $limit;

    /**
     * @param null|string $text
     * @return bool
     */
    private static function isTextNotEmpty($text)
    {
        return isset($text) && is_string($text) && $text != "";
    }

    /**
     * @param null|string $s1
     * @param null|string $s2
     * @param null|string $separator
     * @return null|string
     */
    private static function concat($s1, $s2, $separator)
    {
        $s1NotEmpty = self::isTextNotEmpty($s1);
        $s2NotEmpty = self::isTextNotEmpty($s2);

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

    /**
     * @param null|mixed $value
     * @return string
     */
    private function format($value)
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

    /**
     * @return null|string
     */
    private function getParams()
    {
        $result = null;

        if ($this->params & self::SELECT_SQL_BUFFER_RESULT) {
            $result = self::concat($result, Expr::SQL_BUFFER_RESULT, Expr::SPACE);
        }

        return $result;
    }

    /**
     * @return null|string
     */
    private function getColumns()
    {
        $result = null;

        if ($this->columns) {
            foreach ($this->columns as $column) {
                $result = self::concat($result, $column, Expr::COMMA . Expr::SPACE);
            }
        }

        return $result != null ? $result : Expr::ALL;
    }

    /**
     * @return null|string
     */
    private function getTable()
    {
        return self::isTextNotEmpty($this->table) ? Expr::FROM . Expr::SPACE . $this->table : null;
    }

    /**
     * @return null|string
     */
    private function getJoin()
    {
        $result = null;

        if ($this->join) {
            $table = $this->join->getTable();
            $columnsArray = $this->join->getColumns();
            $columns = null;

            foreach ($columnsArray as $column) {
                $columns = self::concat($columns, $column, Expr::COMMA . Expr::SPACE);
            }
            $result = sprintf(Expr::JOIN, $table, $columns);
        }

        return $result;
    }

    /**
     * @return null|string
     */
    private function getWhere()
    {
        $result = null;

        if ($this->where) {
            /** @var Where $where */
            foreach ($this->where as $where) {
                $column = $where->getColumn();
                $comparison = $where->getComparison();
                $value = $where->getValue();
                if ($value === null) {
                    $value = "";
                }

                if ($comparison == self::COMPARISON_LIKE) {
                    $expr = sprintf(Expr::LIKE, $this->format((string)$value));
                } elseif ($comparison == self::COMPARISON_IN) {
                    if (($value instanceof Builder)) {
                        $expr = sprintf(Expr::IN, $value->build());
                    } else {
                        $valuesArray = explode(Expr::COMMA, (string)$value);

                        $values = null;
                        foreach ($valuesArray as $value) {
                            $value = trim($value);

                            if (floatval($value)) {
                                $value = $this->format((float)$value);
                            } elseif (intval($value)) {
                                $value = $this->format((int)$value);
                            } else {
                                $l = strlen($value);
                                if (($l > 1) && ($value[0] == "'" || $value[0] == '"') && ($value[0] == $value[$l - 1])) {
                                    $value = substr($value, 1, $l - 2);
                                }
                                $value = $this->format($value);
                            }

                            $values = self::concat($values, $value, Expr::COMMA . Expr::SPACE);
                        }

                        $expr = sprintf(Expr::IN, $values);
                    }
                } else {
                    switch ($comparison) {
                        default:
                        case self::COMPARISON_EQUAL:
                            $expr = Expr::EQUAL;
                            break;
                        case self::COMPARISON_NOT_EQUAL:
                            $expr = Expr::NOT_EQUAL;
                            break;
                        case self::COMPARISON_LESS:
                            $expr = Expr::LESS;
                            break;
                        case self::COMPARISON_LESS_OR_EQUAL:
                            $expr = Expr::LESS_OR_EQUAL;
                            break;
                        case self::COMPARISON_GREATER:
                            $expr = Expr::GREATER;
                            break;
                        case self::COMPARISON_GREATER_OR_EQUAL:
                            $expr = Expr::GREATER_OR_EQUAL;
                            break;
                    }

                    $value = $this->format($value);

                    $expr = $expr . Expr::SPACE . $value;
                }

                $expr = $column . Expr::SPACE . $expr;

                $result = self::concat($result, $expr, Expr::SPACE . Expr::EXPR_AND . Expr::SPACE);
            }
        }

        return $result != null ? Expr::WHERE . Expr::SPACE . $result : null;
    }

    /**
     * @return null|string
     */
    private function getGroup()
    {
        $result = null;

        if ($this->group) {
            foreach ($this->group as $column => $column) {
                $result = self::concat($result, $column, Expr::COMMA . Expr::SPACE);
            }
        }

        return $result != null ? Expr::GROUP . Expr::SPACE . $result : null;
    }

    /**
     * @return null|string
     */
    private function getOrder()
    {
        $result = null;

        if ($this->order) {
            /** @var Order $order */
            foreach ($this->order as $column => $order) {
                $collate = $order->getCollate();
                if (self::isTextNotEmpty($collate)) {
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

    /**
     * @return null|string
     */
    private function getLimit()
    {
        return isset($this->limit) ? Expr::LIMIT . Expr::SPACE . $this->limit : null;
    }

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
     * @see Builder::SELECT_SQL_BUFFER_RESULT
     * @return $this
     */
    public function params($params)
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
     * @param string $column Имя колонки.
     * @param null|string $table Имя таблицы, которой принадлежит колонка.
     * @param null|string $alias Псевдоним для колонки.
     *
     * @return $this
     */
    public function column($column, $table = null, $alias = null)
    {
        if (self::isTextNotEmpty($column)) {
            if (self::isTextNotEmpty($table)) {
                $column = $table . Expr::DOT . $column;
            }
            if (self::isTextNotEmpty($alias)) {
                $column = $column . Expr::SPACE . Expr::EXPR_AS . Expr::SPACE . $alias;
            }

            $this->columns[] = $column;
        }
        return $this;
    }

    /**
     * Добавление таблицы в запрос.
     * <p>
     * FROM имя_таблицы. Поддерживается только одна таблица в запросе (кроме присоединённых JOIN).
     *
     * @param string $table
     *
     * @return $this
     */
    public function table($table)
    {
        if (self::isTextNotEmpty($table)) {
            $this->table = $table;
        }
        return $this;
    }

    /**
     * Добавление объединения  в запрос.
     * <p>
     * LEFT JOIN имя_таблицы USING (имя_колонки1, имя_колонки2...).
     *
     * @param string $table Имя таблицы.
     * @param string|array $columns Имя колонки (string) или имена колонок (array).
     *
     * @return $this
     */
    public function join($table, $columns)
    {
        if (self::isTextNotEmpty($table)) {
            if (!is_array($columns)) {
                $columns = array($columns);
            }
            $this->join = new Join($table, $columns);
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
     * @param string $column Имя колонки.
     * @param int $comparison Сравнение.
     * @param mixed $value Значение.
     *
     * @return $this
     *
     * @see Builder::COMPARISON_EQUAL
     * @see Builder::COMPARISON_NOT_EQUAL
     * @see Builder::COMPARISON_LESS
     * @see Builder::COMPARISON_LESS_OR_EQUAL
     * @see Builder::COMPARISON_GREATER
     * @see Builder::COMPARISON_GREATER_OR_EQUAL
     * @see Builder::COMPARISON_LIKE
     * @see Builder::COMPARISON_IN
     */
    public function where($column, $comparison, $value)
    {
        if (self::isTextNotEmpty($column) && $value !== null) {
            $this->where[] = new Where($column, $comparison, $value);
        }
        return $this;
    }

    /**
     * Добавление в запрос условия группировки.
     * <p>
     * GROUP BY имя_колонки_1, имя_колонки_2...
     *
     * @param string $column Имя колонки.
     *
     * @return $this
     */
    public function group($column)
    {
        if (self::isTextNotEmpty($column)) {
            $this->group[$column] = "+";
        }
        return $this;
    }

    /**
     * Добавление в запрос сортировки.
     * <p>
     * ORDER BY имя_колонки_1, имя_колонки_2 DESC, имя_колонки_3 COLLATE latin1_bin.
     *
     * @param string $column Имя колонки
     * @param bool $desc Направление сортировки (ASC|DESC).
     * @param null|string $collate Кодировка колонки.
     *
     * @return $this
     */
    public function order($column, $desc = false, $collate = null)
    {
        if (self::isTextNotEmpty($column)) {
            $this->order[$column] = new Order((bool)$desc, $collate);
        }
        return $this;
    }

    /**
     * Добавление в запрос лимита.
     * <p>
     * Поддерживается только LIMIT число_строк.
     *
     * @param int $count
     *
     * @return $this
     */
    public function limit($count)
    {
        $this->limit = isset($count) && (int)$count > 0 ? (int)$count : null;
        return $this;
    }

    /**
     * Возвращает построенный запрос.
     *
     * @return null|string
     */
    public function build()
    {
        $result = self::concat(Expr::SELECT, $this->getParams(), Expr::SPACE);
        $result = self::concat($result, $this->getColumns(), Expr::SPACE);
        $result = self::concat($result, $this->getTable(), Expr::SPACE);
        $result = self::concat($result, $this->getJoin(), Expr::SPACE);
        $result = self::concat($result, $this->getWhere(), Expr::SPACE);
        $result = self::concat($result, $this->getGroup(), Expr::SPACE);
        $result = self::concat($result, $this->getOrder(), Expr::SPACE);
        $result = self::concat($result, $this->getLimit(), Expr::SPACE);

        return $result;
    }
}
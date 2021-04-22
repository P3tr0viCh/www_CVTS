<?php

namespace builders\query_builder;

class Expr
{
    const SELECT = "SELECT";
    const FROM = "FROM";
    const JOIN = "LEFT JOIN %s USING (%s)";
    const WHERE = "WHERE";
    const GROUP = "GROUP BY";
    const ORDER = "ORDER BY";
    const LIMIT = "LIMIT";
    const COLLATE = "COLLATE";

    const COMMA = ",";
    const DOT = ".";

    const SPACE = " ";

    const ALL = "*";
    const EQUAL = "=";
    const NOT_EQUAL = "<>";
    const LESS = "<";
    const LESS_OR_EQUAL = "<=";
    const GREATER = ">";
    const GREATER_OR_EQUAL = ">=";
    const IN = "IN (%s)";
    const LIKE = "LIKE %s";

    const EXPR_AND = "AND";
    const DESC = "DESC";
    const EXPR_AS = "AS";

    const SQL_BUFFER_RESULT = "SQL_BUFFER_RESULT";
}
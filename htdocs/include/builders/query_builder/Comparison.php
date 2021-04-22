<?php

namespace builders\query_builder;

class Comparison
{
    /**
     * Сравнение WHERE "равно (=)".
     */
    const EQUAL = 0;

    /**
     * Сравнение WHERE "не равно (<>)".
     */
    const NOT_EQUAL = 1;

    /**
     * Сравнение WHERE "меньше (<)".
     */
    const LESS = 2;

    /**
     * Сравнение WHERE "меньше или равно (<=)".
     */
    const LESS_OR_EQUAL = 3;

    /**
     * Сравнение WHERE "больше (>)".
     */
    const GREATER = 4;

    /**
     * Сравнение WHERE " больше или равно (>=)".
     */
    const GREATER_OR_EQUAL = 5;

    /**
     * Сравнение WHERE "LIKE 'значение'".
     */
    const LIKE = 6;

    /**
     * Сравнение WHERE "IN (значение_1, значение_2)".
     */
    const IN = 7;
}
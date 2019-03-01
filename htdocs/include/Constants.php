<?php

class Constants
{
    /**
     * Все железнодорожные весы.
     */
    const SCALE_NUM_ALL_TRAIN_SCALES = 0;

    const SCALE_NUM_REPORT_IRON = -1;
}

class ScaleType
{
    /**
     * Тип весов по умолчанию.
     */
    const DEFAULT_TYPE = 0;
    /**
     * Весы типа "ВМР" (Миксер, Старая разливка и т.п.).
     */
    const WMR = 1981;
    /**
     * Весы типа "ВА" (Автомобильные).
     */
    const AUTO = 2007;
    /**
     * Весы типа "Канатная дорога".
     */
    const KANAT = 2010;
    /**
     * Весы типа "Доменная печь".
     */
    const DP = 20102;
    /**
     * Отчёт по чугуну.
     */
    const IRON = -100;
}

class ScaleClass
{
    /**
     * Класс весов: динамические и статические.
     */
    const CLASS_DYNAMIC_AND_STATIC = 1;
    /**
     * Класс весов: динамические.
     */
    const CLASS_DYNAMIC = 2;
    /**
     * Класс весов: статические.
     */
    const CLASS_STATIC = 3;
}

class ReportType
{
    const TYPE_DEFAULT = 0;
    const CARGO_TYPES = 1;
    const TRAINS = 2;
    const IRON = 3;
}

class ResultType
{
    const VAN_DYNAMIC_BRUTTO = 10;
    const VAN_DYNAMIC_TARE = 11;
    const VAN_STATIC_BRUTTO = 12;
    const VAN_STATIC_TARE = 13;

    const TRAIN_DYNAMIC = 20;
    const TRAIN_DYNAMIC_ONE = 21;

    const AUTO_BRUTTO = 30;
    const AUTO_TARE = 31;

    const KANAT = 40;

    const DP = 50;
    const DP_SUM = 51;

    const CARGO_LIST_DYNAMIC = 60;
    const CARGO_LIST_STATIC = 61;
    const CARGO_LIST_AUTO = 62;

    const COMPARE_DYNAMIC = 70;
    const COMPARE_STATIC = 71;

    const COEFFS = 80;

    const IRON = 90;
}

class ParamName
{
    const SCALE_NUM = 'scale_num';

    const SHOW_DISABLED = 'show_disabled';
    const SHOW_METROLOGY = 'show_metrology';
    const USE_BACKUP = 'use_backup';

    const NEW_DESIGN = 'new_design';

    /**
     * @see ReportType
     */
    const REPORT_TYPE = 'report_type';

    /**
     * @see ResultType
     */
    const RESULT_TYPE = 'result_type';

    const ALL_FIELDS = 'full';
    const TRAIN_NUM = 'train_num';
    const TRAIN_UNIX_TIME = 'train_unix_time';
    const TRAIN_DATETIME = 'train_date_time';
    const DATETIME_START = 'time_start';
    const DATETIME_END = 'time_end';
    const VAN_NUMBER = 'van_number';
    const CARGO_TYPE = 'cargo_type';
    const INVOICE_NUM = 'invoice_num';
    const INVOICE_SUPPLIER = 'invoice_supplier';
    const INVOICE_RECIPIENT = 'invoice_recipient';
    const ONLY_CHARK = 'only_chark';
    const SCALES = 'scales_filter';
    const SHOW_CARGO_DATE = 'show_cargo_date';
    const SHOW_DELTAS = 'show_deltas';
    const ORDER_BY_DATETIME = 'order_by_datetime';
    const COMPARE_FORWARD = 'compare_forward';
    const COMPARE_BY_BRUTTO = 'compare_by_brutto';

    const DATETIME_START_DAY = 'date_time_start_day';
    const DATETIME_START_MONTH = 'date_time_start_month';
    const DATETIME_START_YEAR = 'date_time_start_year';
    const DATETIME_START_HOUR = 'date_time_start_hour';
    const DATETIME_START_MINUTES = 'date_time_start_minutes';
    const DATETIME_END_DAY = 'date_time_end_day';
    const DATETIME_END_MONTH = 'date_time_end_month';
    const DATETIME_END_YEAR = 'date_time_end_year';
    const DATETIME_END_HOUR = 'date_time_end_hour';
    const DATETIME_END_MINUTES = 'date_time_end_minutes';

    const ORDER_BY_DESC = 'order_by_desc';

    const EXCEL_FILENAME = 'filename';
    const EXCEL_DATA = 'data';

    const DEBUG = 'debug';

    const DISABLE_HIDE_CURSOR = 'disable_hide_cursor';

    const NIGHT_MODE = 'night_mode';

    const SHOW_SECONDS = 'show_seconds';
    const DATE_FORMAT = 'date_format';

    const DEPARTMENT = 'department';

    const SHOW_TEMP = 'show_temp';
}
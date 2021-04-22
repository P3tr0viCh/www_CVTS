<?php

class Constants
{
    const DEBUG_SHOW_ERROR = false;
    const DEBUG_SHOW_QUERY = false;

    /**
     * Все железнодорожные весы.
     */
    const SCALE_NUM_ALL_TRAIN_SCALES = 0;

    const SCALE_NUM_REPORT_VANLIST = -100;

    const SCALE_NUM_REPORT_IRON = -200;
    const SCALE_NUM_REPORT_IRON_CONTROL = -201;

    const SCALE_NUM_REPORT_SENSORS_INFO = -300;

    /**
     * Максимальное число строк для страницы результата.
     */
    const RESULT_MAX_ROWS = 32000;

    const SENSORS_M_MAX_COUNT = 16;

    const SENSORS_T_MAX_COUNT = 8;
    const COMPARE_VALUE_WARNING_YELLOW = 1;
    const COMPARE_VALUE_WARNING_RED = 2;
}

/**
 * Пороговые значения.
 */
class Thresholds {
    const COMPARE_VALUE_WARNING_YELLOW = 1;
    const COMPARE_VALUE_WARNING_RED = 2;

    const IRON_CONTROL_DIFF_DYN_STA_WARNING_YELLOW = 1;
    const IRON_CONTROL_DIFF_DYN_STA_WARNING_RED = 2;
    const IRON_CONTROL_DIFF_SIDE_WARNING_YELLOW = 3;
    const IRON_CONTROL_DIFF_SIDE_WARNING_RED = 5;
    const IRON_CONTROL_DIFF_CARRIAGE_WARNING_YELLOW = 3;
    const IRON_CONTROL_DIFF_CARRIAGE_WARNING_RED = 5;
    const IRON_CONTROL_AVG_VALUE_WARNING_YELLOW = 0.05;
    const IRON_CONTROL_AVG_VALUE_WARNING_RED = 0.10;
    const IRON_CONTROL_SUM_VALUE_WARNING_YELLOW = 5;
    const IRON_CONTROL_SUM_VALUE_WARNING_RED = 10;
}

/**
 * Номера весов.
 */
class ScaleNums
{
    /**
     * Провеска чугуна: ЭСПЦ.
     */
    const IRON_ESPC = '10, 97';
    /**
     * Провеска чугуна: Разливка.
     */
    const IRON_RAZL = '182, 1043, 98';
    /**
     * Провеска чугуна: СХЧ.
     */
    const IRON_SHCH = '156, 31, 41';

    /**
     * Контрольная провеска чугуна: весы в динамике (Сортировка, Прокат, Копровая).
     */
    const IRON_COMPARE_DYN = '150, 164, 12, 125, 96';
    /**
     * Контрольная провеска чугуна: весы в статике (СХЧ).
     */
    const IRON_COMPARE_STA = '156, 31, 41';
}

/**
 * Периоды времени.
 */
class TimePeriods
{
    /**
     * Сравнение массы: глубина поиска в днях.
     */
    const COMPARE = 31;

    /**
     * Контрольная провеска чугуна: глубина поиска в днях.
     */
    const IRON_COMPARE = 3;
}

/**
 * Роды грузов.
 */
class CargoTypes
{
    const CHARK = 'Кокс';

    /**
     * Провеска чугуна.
     */
    const IRON = 'Чугун';
    const IRON_INGOT = "'Чугун передельный чушковый', 'Чугун литейный чушковый'";

    /**
     * Контрольная провеска чугуна.
     */
    const IRON_COMPARE_DYN = 'Чугун (контрольная провеска)';
    const IRON_COMPARE_STA = 'Чугун';
}

/**
 * Отправители и получатели.
 */
class SuppliersAndRecipients
{
    /**
     * Провеска чугуна.
     */
    const IRON_SUPPLIER = 'Доменный%';
    const IRON_RECIPIENT = 'ЭСПЦ';
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
    const IRON = Constants::SCALE_NUM_REPORT_IRON;
    /**
     * Контрольная провеска чугуна.
     */
    const IRON_CONTROL = Constants::SCALE_NUM_REPORT_IRON_CONTROL;
    /**
     * Сводная информация по списку номеров вагонов.
     */
    const VANLIST = Constants::SCALE_NUM_REPORT_VANLIST;
}

class ScaleClass
{
    /**
     * Класс весов: статические.
     */
    const CLASS_STATIC = 0;
    /**
     * Класс весов: динамические.
     */
    const CLASS_DYNAMIC = 1;
    /**
     * Класс весов: динамические и статические.
     */
    const CLASS_DYNAMIC_AND_STATIC = 2;
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
    const SENSORS_ZEROS = 81;
    const SENSORS_TEMPS = 82;
    const SENSORS_STATUS = 83;

    const IRON = 90;
    const IRON_CONTROL = 91;

    const VANLIST_WEIGHS = 100;
    const VANLIST_LAST_TARE = 101;

    const SENSORS_INFO = 200;
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
    const SHOW_DELTAS_MI_3115 = 'show_deltas_mi_3115';
    const SHOW_TOTAL_SUMS = 'show_total_sums';
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

    const ORDER_BY_DATETIME_ASC = 'order_by_datetime_asc';

    const DATETIME_FROM_20_TO_20 = 'datetime_from_20_to_20';

    const VANLIST = 'vanlist';

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
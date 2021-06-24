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
    const SCALE_NUM_REPORT_SLAG_CONTROL = -202;

    const SCALE_NUM_REPORT_SENSORS_INFO = -300;

    /**
     * Максимальное число строк для страницы результата.
     */
    const RESULT_MAX_ROWS = 32000;

    const SENSORS_M_MAX_COUNT = 16;
    const SENSORS_T_MAX_COUNT = 8;

    const MY_OPERATOR_ID = 0;
}

/**
 * Пороговые значения.
 */
class Thresholds
{
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

    const SLAG_CONTROL_DIFF_DYN_STA_WARNING_YELLOW = 1;
    const SLAG_CONTROL_DIFF_DYN_STA_WARNING_RED = 2;
    const SLAG_CONTROL_DIFF_SIDE_WARNING_YELLOW = 3;
    const SLAG_CONTROL_DIFF_SIDE_WARNING_RED = 5;
    const SLAG_CONTROL_DIFF_CARRIAGE_WARNING_YELLOW = 3;
    const SLAG_CONTROL_DIFF_CARRIAGE_WARNING_RED = 5;
    const SLAG_CONTROL_AVG_VALUE_WARNING_YELLOW = 0.05;
    const SLAG_CONTROL_AVG_VALUE_WARNING_RED = 0.10;
    const SLAG_CONTROL_SUM_VALUE_WARNING_YELLOW = 5;
    const SLAG_CONTROL_SUM_VALUE_WARNING_RED = 10;
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

    /**
     * Контрольная провеска шлака: весы в динамике (Сортировка).
     */
    const SLAG_COMPARE_DYN = '150, 164';
    /**
     * Контрольная провеска шлака: весы в статике (ШПУ-1).
     */
    const SLAG_COMPARE_STA = '99';
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

    /**
     * Контрольная провеска шлака: глубина поиска в днях.
     */
    const SLAG_COMPARE = 3;
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
    /**
     * Контрольная провеска шлака.
     */
    const SLAG_COMPARE_DYN = 'Шлак (контрольная провеска)';
    const SLAG_COMPARE_STA = 'Шлак гранулированный';
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
     * Контрольная провеска шлака.
     */
    const SLAG_CONTROL = Constants::SCALE_NUM_REPORT_SLAG_CONTROL;
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
    const SLAG_CONTROL = 92;

    const VANLIST_WEIGHS = 100;
    const VANLIST_LAST_TARE = 101;

    const SENSORS_INFO = 200;
}
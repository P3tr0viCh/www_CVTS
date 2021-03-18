<?php
/** @noinspection PhpIllegalPsrClassPathInspection */
/** @noinspection PhpDuplicateSwitchCaseBodyInspection */
require_once "MySQLConnection.php";
require_once "Strings.php";
require_once "Constants.php";

use database\Columns as C;
use JetBrains\PhpStorm\Pure;
use Strings as S;

function concatStrings($s1, $s2, $separator): string
{
    if (($s1 == "") && ($s2 == "")) return "";
    elseif ($s1 == "") return $s2;
    elseif ($s2 == "") return $s1;
    else return $s1 . $separator . $s2;
}

/**
 * Class FieldInfo
 */
class FieldInfo
{
    public string $name;
    public bool $visible;
    public bool $leftAlign;
}

/**
 * @param mysqli_result $queryResult
 * @param bool $newDesign
 * @param bool $full
 * @param ScaleInfo $scaleInfo
 * @param int $type
 * @return FieldInfo[]
 */
function getFieldsInfo(mysqli_result $queryResult, bool $newDesign, bool $full, ScaleInfo $scaleInfo, int $type): array
{
    $result = array();

    foreach ($queryResult->fetch_fields() as $field) {
        $fieldInfo = new FieldInfo();

        $fieldInfo->name = $field->name;
        $fieldInfo->visible = $full || isFieldVisible($fieldInfo->name, $scaleInfo, $type);
        $fieldInfo->leftAlign = isFieldLeftAlign($newDesign, $fieldInfo->name);

        $result[] = $fieldInfo;
    }

    return $result;
}

#[Pure] function formatFieldValue(string $fieldName, $fieldValue, bool $full)
{
    if ($fieldValue != "") {
        switch ($fieldName) {
            case C::DATETIME:
            case C::DATETIME_END:
            case C::DATETIME_T:
            case C::DATETIME_OPERATOR:
            case C::DATETIME_TARE:
            case C::DATETIME_SHIPMENT:
            case C::DATETIME_FAILURE:
            case C::DATETIME_CARGO:

            case C::MI_TARE_DYN_DATETIME:
            case C::MI_TARE_STA_DATETIME:

            case C::IRON_CONTROL_DATETIME_STA:
            case C::IRON_CONTROL_DATETIME_DYN:
                if ($fieldValue == "0000-00-00 00:00:00" ||
                    $fieldValue == "1899-12-30 00:00:00") {
                    return S::TEXT_TABLE_CELL_EMPTY;
                }

                $year = substr($fieldValue, 0, 4);
                $month = substr($fieldValue, 5, 2);
                $day = substr($fieldValue, 8, 2);
                $hour = substr($fieldValue, 11, 2);
                $minute = substr($fieldValue, 14, 2);
                $second = substr($fieldValue, 17, 2);

                $d = $day . "." . $month . "." . $year;
                $d .= "&nbsp;";
                $d .= $hour . ":" . $minute;
                if ($full) {
                    $d .= ":" . $second;
                }

                return $d;

            case C::IRON_DATE:
                return $fieldValue;

            case C::TRAIN_NUMBER:
            case C::VAN_NUMBER:
            case C::AUTO_NUMBER:
                return $fieldValue;

            case C::CARRYING:
            case C::LOAD_NORM:
            case C::VOLUME:
            case C::TARE:
            case C::TARE_NEAR_SIDE:
            case C::TARE_FAR_SIDE:
            case C::TARE_FIRST_CARRIAGE:
            case C::TARE_SECOND_CARRIAGE:
            case C::TARE_MANUAL:
            case C::TARE_DYNAMIC:
            case C::TARE_STATIC:
            case C::BRUTTO:
            case C::BRUTTO_NEAR_SIDE:
            case C::BRUTTO_FAR_SIDE:
            case C::BRUTTO_FIRST_CARRIAGE:
            case C::BRUTTO_SECOND_CARRIAGE:
            case C::SIDE_DIFFERENCE:  // Разница между бортами
            case C::CARRIAGE_DIFFERENCE:  // Разница между тележками
            case C::INVOICE_NETTO:
            case C::INVOICE_TARE:
            case C::ACCELERATION:

            case C::IRON_ESPC_RAZL:
            case C::IRON_ESPC:
            case C::IRON_RAZL:
            case C::IRON_SHCH:
            case C::IRON_INGOT:

            case C::IRON_CONTROL_NETTO_STA:
            case C::IRON_CONTROL_NETTO_DYN:
            case C::IRON_CONTROL_DIFF_DYN_CARR:
            case C::IRON_CONTROL_DIFF_SIDE:
            case C::IRON_CONTROL_DIFF_CARRIAGE:
                return num_fmt($fieldValue, 2);

            case C::NETTO:

            case C::IRON_CONTROL_DIFF_DYN_STA:
                $s = num_fmt($fieldValue, 2);
                return "<b>" . $s . "</b>";

            case C::MASS:
                return num_fmt($fieldValue, 4);

            case C::OVERLOAD:
            case C::INVOICE_OVERLOAD:
            case C::COMPARE:
                $overload = num_fmt($fieldValue, 2);
                if ($fieldValue > 0) {
                    $overload = "+" . $overload;
                }
                if ($fieldName == C::COMPARE) {
                    $overload = "<b>" . $overload . "</b>";
                }
                return $overload;

            case C::VELOCITY:
                $velocity = num_fmt(abs($fieldValue), 1);
                return $fieldValue > 0 ? $velocity . " &gt;&gt;&gt;" : "&lt;&lt;&lt; " . $velocity;

            case C::WMODE:
                switch ($fieldValue) {
                    case 1:
                        return S::TEXT_SCALE_CLASS_DYNAMIC;
                    case 2:
                        return S::TEXT_SCALE_CLASS_STATIC;
                    default:
                        return S::TEXT_SCALE_CLASS_UNKNOWN;
                }

            case C::OPERATION_TYPE:
                switch ($fieldValue) {
                    case 10:
                        return S::TEXT_OPERATION_TYPE_CALIBRATION_DYNAMIC;
                    case 20:
                        return S::TEXT_OPERATION_TYPE_CALIBRATION_STATIC;
                    case 11:
                        return S::TEXT_OPERATION_TYPE_VERIFICATION_DYNAMIC;
                    case 21:
                        return S::TEXT_OPERATION_TYPE_VERIFICATION_STATIC;
                    case 40:
                        return S::TEXT_OPERATION_TYPE_MAINTENANCE;
                    case 50:
                        return S::TEXT_OPERATION_TYPE_REPAIR;
                    default:
                        return S::TEXT_OPERATION_TYPE_UNKNOWN;
                }

            case C::TARE_TYPE:
                switch ($fieldValue) {
                    case 0:
                        return S::TEXT_TARE_TYPE_MANUAL;
                    case 1:
                        return S::TEXT_TARE_TYPE_DYNAMIC;
                    case 2:
                        return S::TEXT_TARE_TYPE_STATIC;
                    default:
                        return S::TEXT_TARE_TYPE_UNKNOWN;
                }

            case C::LEFT_SIDE:
                switch ($fieldValue) {
                    case 0:
                        return S::TEXT_SIDE_RIGHT;
                    case 1:
                        return S::TEXT_SIDE_LEFT;
                    default:
                        return S::TEXT_SIDE_UNKNOWN;
                }

            case C::COEFFICIENT_P1:
            case C::COEFFICIENT_P2:
                return num_fmt($fieldValue, 5);
            case C::COEFFICIENT_Q1:
            case C::COEFFICIENT_Q2:
                return num_fmt($fieldValue, 0);
            case C::COEFFICIENT_T1:
            case C::COEFFICIENT_T2:
                return num_fmt($fieldValue, 1);

            case C::TEMPERATURE_1:
            case C::TEMPERATURE_2:
            case C::TEMPERATURE_3:
            case C::TEMPERATURE_4:
            case C::TEMPERATURE_5:
            case C::TEMPERATURE_6:
            case C::TEMPERATURE_7:
            case C::TEMPERATURE_8:
                return num_fmt($fieldValue, 0);

            case C::SCALE_CLASS_STATIC:
            case C::SCALE_CLASS_DYNAMIC:
                /**
                 * Значение задаётся в @uses  \QueryScales::getScaleClass.
                 * Значения от 0 до 100 соответствуют железнодорожным весам в динамике,
                 * от 100 до 199 - статическим железнодорожным весам,
                 * от 200 до 299 - автомобильным весам,
                 * от 300 до 399 - доменным печам,
                 * от 400 до 499 - вагонеточным весам.
                 */
                switch ($fieldValue) {
                    case 0:
                        return S::TEXT_SCALE_CLASS_DYNAMIC_0;
                    case 1:
                        return S::TEXT_SCALE_CLASS_DYNAMIC_1;
                    case 2:
                        return S::TEXT_SCALE_CLASS_DYNAMIC_2;
                    case 3:
                        return S::TEXT_SCALE_CLASS_DYNAMIC_3;
                    case 4:
                        return S::TEXT_SCALE_CLASS_DYNAMIC_4;

                    case 100:
                    case 200:
                    case 300:
                        return S::TEXT_SCALE_CLASS_STATIC_0;
                    case 101:
                    case 201:
                    case 301:
                        return S::TEXT_SCALE_CLASS_STATIC_1;
                    case 102:
                    case 202:
                    case 302:
                        return S::TEXT_SCALE_CLASS_STATIC_2;

                    case 400:
                        return S::TEXT_SCALE_CLASS_DYNAMIC_3;
                    default:
                        return S::TEXT_SIDE_UNKNOWN;
                }

            case C::TARE_SCALE_NUMBER:
            case C::MI_TARE_DYN_SCALES:
            case C::MI_TARE_STA_SCALES:
                return $fieldValue != 0 ? $fieldValue : S::TEXT_TABLE_CELL_EMPTY;

            case C::MI_DELTA_ABS_BRUTTO:
            case C::MI_DELTA_ABS_BRUTTO_E:
            case C::MI_DELTA_ABS_TARE:
            case C::MI_DELTA_ABS_TARE_E:
            case C::MI_DELTA:
            case C::MI_DELTA_E:
            case C::MI_TARE_DYN:
            case C::MI_DELTA_ABS_TARE_DYN:
            case C::MI_DELTA_ABS_TARE_DYN_E:
            case C::MI_DELTA_DYN:
            case C::MI_DELTA_DYN_E:
            case C::MI_TARE_STA:
            case C::MI_DELTA_ABS_TARE_STA:
            case C::MI_DELTA_ABS_TARE_STA_E:
            case C::MI_DELTA_STA:
            case C::MI_DELTA_STA_E:
                if ($fieldValue[0] == '-') {
                    if ($full) {
                        return 'E_' . substr((int)$fieldValue, 1);
                    } else {
                        return S::TEXT_TABLE_CELL_EMPTY;
                    }
                } else {
                    switch ($fieldName) {
                        case C::MI_TARE_DYN:
                        case C::MI_TARE_STA:
                            return num_fmt($fieldValue, 2);

                        case C::MI_DELTA:
                        case C::MI_DELTA_DYN:
                        case C::MI_DELTA_STA:
                            return num_fmt($fieldValue, 1);

                        case C::MI_DELTA_E:
                        case C::MI_DELTA_DYN_E:
                        case C::MI_DELTA_STA_E:
                            return num_fmt($fieldValue, 2);

                        case C::MI_DELTA_ABS_BRUTTO:
                        case C::MI_DELTA_ABS_TARE:
                        case C::MI_DELTA_ABS_TARE_DYN:
                        case C::MI_DELTA_ABS_TARE_STA:
                            return num_fmt($fieldValue, 0);

                        case C::MI_DELTA_ABS_BRUTTO_E:
                        case C::MI_DELTA_ABS_TARE_E:
                        case C::MI_DELTA_ABS_TARE_DYN_E:
                        case C::MI_DELTA_ABS_TARE_STA_E:
                            return num_fmt($fieldValue, 2);

                        default:
                            return $fieldValue;
                    }
                }

            case C::MI_3115_LOSS_SUPPLIER:
            case C::MI_3115_DELTA_SUPPLIER:
            case C::MI_3115_DELTA_FROM_TABLES:
            case C::MI_3115_DELTA_FOR_STATIONS:
            case C::MI_3115_DELTA:
                return num_fmt($fieldValue, 2) . '%';
            case C::MI_3115_TOLERANCE:
                return num_fmt($fieldValue, 2);
            case C::MI_3115_RESULT:
                if ($fieldValue == 0.0) {
                    return S::TEXT_DELTA_MI_3115_OK;
                } else {
                    $value = num_fmt($fieldValue, 3);

                    if ($fieldValue > 0) {
                        $value = "+" . $value;
                    }

                    return $value;
                }

            case C::HUMIDITY:
                return num_fmt($fieldValue, 2) . '%';

            default:
                return $fieldValue;
        }
    } else {
        return S::TEXT_TABLE_CELL_EMPTY;
    }
}

#[Pure] function num_fmt(float $number, int $decimals): string
{
    return number_format($number, $decimals, S::DEC_POINT, "");
}

#[Pure] function isFieldVisible(string $fieldName, ScaleInfo $scalesInfo, int $resultType): bool
{
    switch ($fieldName) {
        case C::SCALE_NUM:
            switch ($scalesInfo->getScaleNum()) {
                case Constants::SCALE_NUM_ALL_TRAIN_SCALES:
                case Constants::SCALE_NUM_REPORT_VANLIST:
                    return true;
                default:
                    return false;
            }
        case C::DATETIME:
        case C::TRAIN_NUMBER:
        case C::BRUTTO:
        case C::TARE:
        case C::NETTO:
        case C::VAN_COUNT:
        case C::OPERATOR:

        case C::PRODUCT:
        case C::LEFT_SIDE:

        case C::VAN_NUMBER:
        case C::VAN_TYPE:
        case C::CARGO_TYPE:
        case C::DATETIME_CARGO:
        case C::INVOICE_NUMBER:

        case C::DATETIME_FAILURE:
        case C::WMODE:
        case C::MESSAGE:

        case C::UNIT_NUMBER:
        case C::DATETIME_T:
        case C::OPERATION_TYPE:
        case C::ACCURACY_CLASS:
        case C::DISCRETENESS:
        case C::COEFFICIENT_P1:
        case C::COEFFICIENT_Q1:
        case C::COEFFICIENT_T1:
        case C::COEFFICIENT_P2:
        case C::COEFFICIENT_Q2:
        case C::COEFFICIENT_T2:
        case C::VERIFIER:
        case C::COMMENT:

        case C::INVOICE_SUPPLIER:    // Грузоотправитель или Номер печи
        case C::INVOICE_RECIPIENT:   // Грузополучатель  или Получатель

        case C::SIDE_DIFFERENCE:  // Разница между бортами
        case C::CARRIAGE_DIFFERENCE:  // Разница между тележками
            return true;

        case C::OVERLOAD:    // Перегруз
        case C::CARRYING:    // Грузоподъемность
            return
                $scalesInfo->getType() == ScaleType::DEFAULT_TYPE ||
                $resultType == ResultType::COMPARE_DYNAMIC ||
                $resultType == ResultType::COMPARE_STATIC ||
                $resultType == ResultType::IRON_CONTROL;
        case C::DEPART_STATION:     // Станция отправления
        case C::PURPOSE_STATION:    // Станция назначения
            return $scalesInfo->getType() == ScaleType::DEFAULT_TYPE;

        case C::INVOICE_NETTO:      // Нетто по накладной или Чистый вес
        case C::INVOICE_TARE:       // Тара по накладной  или Тара ПОСЛЕ
        case C::INVOICE_OVERLOAD:   // Перегруз по накладной или Потери
            return
                ($scalesInfo->getType() == ScaleType::WMR) ||
                ($scalesInfo->getType() == ScaleType::AUTO);

        case C::AUTO_NUMBER:
        case C::DRIVER:
        case C::DATETIME_TARE:
            return true;

        case C::SEQUENCE_NUMBER:
            return
                ($resultType != ResultType::TRAIN_DYNAMIC) &&
                ($resultType != ResultType::KANAT);

        case C::DATETIME_END:
            return $resultType == ResultType::COEFFS;

        case C::MI_DELTA_ABS_BRUTTO:
        case C::MI_DELTA_ABS_TARE:
        case C::MI_DELTA:
        case C::MI_TARE_DYN:
        case C::MI_TARE_DYN_SCALES:
        case C::MI_TARE_DYN_DATETIME:
        case C::MI_DELTA_ABS_TARE_DYN:
        case C::MI_DELTA_DYN:
        case C::MI_TARE_STA:
        case C::MI_TARE_STA_SCALES:
        case C::MI_TARE_STA_DATETIME:
        case C::MI_DELTA_ABS_TARE_STA:
        case C::MI_DELTA_STA:
            return true;

        case C::MI_3115_LOSS_SUPPLIER:
        case C::MI_3115_DELTA_SUPPLIER:
        case C::MI_3115_DELTA_FROM_TABLES:
        case C::MI_3115_DELTA_FOR_STATIONS:
        case C::MI_3115_DELTA:
        case C::MI_3115_TOLERANCE:
        case C::MI_3115_RESULT:
            return true;

        case C::IRON_DATE:
        case C::IRON_ESPC_RAZL:
        case C::IRON_ESPC:
        case C::IRON_RAZL:
        case C::IRON_SHCH:
        case C::IRON_INGOT:
            return true;

        case C::IRON_CONTROL_SCALES_STA:
        case C::IRON_CONTROL_DATETIME_STA:
        case C::IRON_CONTROL_SCALES_DYN:
        case C::IRON_CONTROL_DATETIME_DYN:
        case C::IRON_CONTROL_NETTO_STA:
        case C::IRON_CONTROL_NETTO_DYN:
        case C::IRON_CONTROL_DIFF_DYN_CARR:
        case C::IRON_CONTROL_DIFF_DYN_STA:
        case C::IRON_CONTROL_DIFF_SIDE:
        case C::IRON_CONTROL_DIFF_CARRIAGE:
            return true;

        default:
            return false;
    }
}

#[Pure] function columnName(string $fieldName, int $scaleType, int $resultType = null): string
{
    switch ($fieldName) {
        case C::TRAIN_NUM:
            return ColumnsStrings::TRAIN_NUM;

        case C::SCALE_NUM:
            return ColumnsStrings::SCALE_NUM;
        case C::SCALE_MIN_CAPACITY:
            return ColumnsStrings::SCALE_MIN_CAPACITY;
        case C::SCALE_MAX_CAPACITY:
            return ColumnsStrings::SCALE_MAX_CAPACITY;
        case C::SCALE_DISCRETENESS:
            return ColumnsStrings::SCALE_DISCRETENESS;

        case C::UNIX_TIME:
            return ColumnsStrings::UNIX_TIME;
        case C::DATETIME:
            return ColumnsStrings::DATETIME;
        case C::DATETIME_END:
            return $resultType == ResultType::COEFFS ? ColumnsStrings::DATETIME : ColumnsStrings::DATETIME_END;
        case C::TRAIN_NUMBER:
            return ColumnsStrings::TRAIN_NUMBER;
        case C::CARRYING:
            return ColumnsStrings::CARRYING;
        case C::LOAD_NORM:
            return ColumnsStrings::LOAD_NORM;
        case C::VOLUME:
            return ColumnsStrings::VOLUME;
        case C::TARE:
            return $scaleType == ScaleType::WMR ? ColumnsStrings::TARE_WMR : ColumnsStrings::TARE;
        case C::BRUTTO:
            return ColumnsStrings::BRUTTO;
        case C::NETTO:
            return ColumnsStrings::NETTO;
        case C::OVERLOAD:
            return ColumnsStrings::OVERLOAD;
        case C::VAN_COUNT:
            return ColumnsStrings::VAN_COUNT;
        case C::VELOCITY:
            return ColumnsStrings::VELOCITY;
        case C::OPERATOR:
            return ColumnsStrings::OPERATOR;
        case C::OPERATOR_TAB_NUMBER:
            return ColumnsStrings::OPERATOR_TAB_NUMBER;
        case C::OPERATOR_SHIFT_NUMBER:
            return ColumnsStrings::OPERATOR_SHIFT_NUMBER;
        case C::OPERATOR_SHIFT_SYMBOL:
            return ColumnsStrings::OPERATOR_SHIFT_SYMBOL;
        case C::DATETIME_OPERATOR:
            return ColumnsStrings::DATETIME_OPERATOR;
        case C::RAIL_PATH:
            return ColumnsStrings::RAIL_PATH;
        case C::STATUS:
            return ColumnsStrings::STATUS;
        case C::SEQUENCE_NUMBER:
            return ColumnsStrings::SEQUENCE_NUMBER;
        case C::VAN_NUMBER:
            return ColumnsStrings::VAN_NUMBER;
        case C::VAN_TYPE:
            return ColumnsStrings::VAN_TYPE;
        case C::TARE_MANUAL:
            return ColumnsStrings::TARE_MANUAL;
        case C::TARE_DYNAMIC:
            return ColumnsStrings::TARE_DYNAMIC;
        case C::TARE_STATIC:
            return ColumnsStrings::TARE_STATIC;
        case C::TARE_TYPE:
            return ColumnsStrings::TARE_TYPE;
        case C::TARE_SCALE_NUMBER:
            return ColumnsStrings::TARE_SCALE_NUMBER;
        case C::DATETIME_TARE:
            return ColumnsStrings::DATETIME_TARE;
        case C::BRUTTO_NEAR_SIDE:
            return ColumnsStrings::BRUTTO_NEAR_SIDE;
        case C::BRUTTO_FAR_SIDE:
            return ColumnsStrings::BRUTTO_FAR_SIDE;
        case C::BRUTTO_FIRST_CARRIAGE:
            return ColumnsStrings::BRUTTO_FIRST_CARRIAGE;
        case C::BRUTTO_SECOND_CARRIAGE:
            return ColumnsStrings::BRUTTO_SECOND_CARRIAGE;
        case C::SIDE_DIFFERENCE:
            return ColumnsStrings::SIDE_DIFFERENCE;
        case C::CARRIAGE_DIFFERENCE:
            return ColumnsStrings::CARRIAGE_DIFFERENCE;
        case C::MASS:
            return ColumnsStrings::MASS;
        case C::ACCELERATION:
            return ColumnsStrings::ACCELERATION;
        case C::CARGO_TYPE:
            return ColumnsStrings::CARGO_TYPE;
        case C::CARGO_TYPE_CODE:
            return ColumnsStrings::CARGO_TYPE_CODE;
        case C::DATETIME_CARGO:
            return ColumnsStrings::DATETIME_CARGO;
        case C::AXIS_COUNT:
            return ColumnsStrings::AXIS_COUNT;
        case C::COUNTRY:
            return ColumnsStrings::COUNTRY;
        case C::DEPART_STATION:
            return ColumnsStrings::DEPART_STATION;
        case C::DEPART_STATION_CODE:
            return ColumnsStrings::DEPART_STATION_CODE;
        case C::PURPOSE_STATION:
            return ColumnsStrings::PURPOSE_STATION;
        case C::PURPOSE_STATION_CODE:
            return ColumnsStrings::PURPOSE_STATION_CODE;
        case C::DATETIME_SHIPMENT:
            return ColumnsStrings::DATETIME_SHIPMENT;
        case C::INVOICE_NUMBER:
            return $scaleType == ScaleType::WMR ? ColumnsStrings::INVOICE_NUMBER_WMR : ColumnsStrings::INVOICE_NUMBER;
        case C::INVOICE_SUPPLIER:
            return $scaleType == ScaleType::WMR ? ColumnsStrings::INVOICE_SUPPLIER_WMR : ColumnsStrings::INVOICE_SUPPLIER;
        case C::INVOICE_RECIPIENT:
            return ColumnsStrings::INVOICE_RECIPIENT;
        case C::INVOICE_NETTO:
            return $scaleType == ScaleType::WMR ? ColumnsStrings::INVOICE_NETTO_WMR : ColumnsStrings::INVOICE_NETTO;
        case C::INVOICE_TARE:
            return $scaleType == ScaleType::WMR ? ColumnsStrings::INVOICE_TARE_WMR : ColumnsStrings::INVOICE_TARE;
        case C::INVOICE_OVERLOAD:
            return $scaleType == ScaleType::WMR ? ColumnsStrings::INVOICE_OVERLOAD_WMR : ColumnsStrings::INVOICE_OVERLOAD;
        case C::LOADING_GROUP:
            return ColumnsStrings::LOADING_GROUP;
        case C::LOADING_PLACE:
            return ColumnsStrings::LOADING_PLACE;
        case C::TARE_NEAR_SIDE:
            return ColumnsStrings::TARE_NEAR_SIDE;
        case C::TARE_FAR_SIDE:
            return ColumnsStrings::TARE_FAR_SIDE;
        case C::TARE_FIRST_CARRIAGE:
            return ColumnsStrings::TARE_FIRST_CARRIAGE;
        case C::TARE_SECOND_CARRIAGE:
            return ColumnsStrings::TARE_SECOND_CARRIAGE;
        case C::DATETIME_FAILURE:
            return ColumnsStrings::DATETIME_FAILURE;
        case C::WMODE:
            return ColumnsStrings::SCALE_CLASS;
        case C::MESSAGE:
            return ColumnsStrings::MESSAGE;
        case C::UNIX_TIME_END:
            return ColumnsStrings::UNIX_TIME_END;
        case C::UNIT_NUMBER:
            return ColumnsStrings::UNIT_NUMBER;
        case C::DATETIME_T:
            return ColumnsStrings::DATETIME_T;
        case C::OPERATION_TYPE:
            return ColumnsStrings::OPERATION_TYPE;
        case C::ACCURACY_CLASS:
            return ColumnsStrings::ACCURACY_CLASS;
        case C::DISCRETENESS:
            return ColumnsStrings::DISCRETENESS;
        case C::COEFFICIENT_P1:
            return ColumnsStrings::COEFFICIENT_P1;
        case C::COEFFICIENT_Q1:
            return ColumnsStrings::COEFFICIENT_Q1;
        case C::COEFFICIENT_T1:
            return ColumnsStrings::COEFFICIENT_T1;
        case C::COEFFICIENT_P2:
            return ColumnsStrings::COEFFICIENT_P2;
        case C::COEFFICIENT_Q2:
            return ColumnsStrings::COEFFICIENT_Q2;
        case C::COEFFICIENT_T2:
            return ColumnsStrings::COEFFICIENT_T2;
        case C::TEMPERATURE_1:
            return ColumnsStrings::TEMPERATURE_1;
        case C::TEMPERATURE_2:
            return ColumnsStrings::TEMPERATURE_2;
        case C::TEMPERATURE_3:
            return ColumnsStrings::TEMPERATURE_3;
        case C::TEMPERATURE_4:
            return ColumnsStrings::TEMPERATURE_4;
        case C::TEMPERATURE_5:
            return ColumnsStrings::TEMPERATURE_5;
        case C::TEMPERATURE_6:
            return ColumnsStrings::TEMPERATURE_6;
        case C::TEMPERATURE_7:
            return ColumnsStrings::TEMPERATURE_7;
        case C::TEMPERATURE_8:
            return ColumnsStrings::TEMPERATURE_8;
        case C::VERIFIER:
            return ColumnsStrings::VERIFIER;
        case C::COMMENT:
            return ColumnsStrings::COMMENT;

        case C::AUTO_NUMBER:
            return ColumnsStrings::AUTO_NUMBER;
        case C::DRIVER:
            return ColumnsStrings::DRIVER;

        case C::WEIGH_NAME:
            return ColumnsStrings::WEIGH_NAME;
        case C::WEIGH_NAME_CODE:
            return ColumnsStrings::WEIGH_NAME_CODE;
        case C::PRODUCT:
            return ColumnsStrings::PRODUCT;
        case C::PRODUCT_CODE:
            return ColumnsStrings::PRODUCT_CODE;
        case C::LEFT_SIDE:
            return ColumnsStrings::LEFT_SIDE;
        case C::PART_CODE:
            return ColumnsStrings::PART_CODE;
        case C::HUMIDITY:
            return ColumnsStrings::HUMIDITY;
        case C::COUNT_ID:
            return ColumnsStrings::COUNT_ID;

        case C::COMPARE:
            return ColumnsStrings::COMPARE;

        case C::MI_DELTA_ABS_BRUTTO:
            return ColumnsStrings::MI_DELTA_ABS_BRUTTO;
        case C::MI_DELTA_ABS_BRUTTO_E:
            return ColumnsStrings::MI_DELTA_ABS_BRUTTO_E;
        case C::MI_DELTA_ABS_TARE:
            return ColumnsStrings::MI_DELTA_ABS_TARE;
        case C::MI_DELTA_ABS_TARE_E:
            return ColumnsStrings::MI_DELTA_ABS_TARE_E;
        case C::MI_DELTA:
            return ColumnsStrings::MI_DELTA;
        case C::MI_DELTA_E:
            return ColumnsStrings::MI_DELTA_E;
        case C::MI_TARE_DYN:
            return ColumnsStrings::MI_TARE_DYN;
        case C::MI_TARE_DYN_SCALES:
            return ColumnsStrings::MI_TARE_DYN_SCALES;
        case C::MI_TARE_DYN_DATETIME:
            return ColumnsStrings::MI_TARE_DYN_BDATETIME;
        case C::MI_DELTA_ABS_TARE_DYN:
            return ColumnsStrings::MI_DELTA_ABS_TARE_DYN;
        case C::MI_DELTA_ABS_TARE_DYN_E:
            return ColumnsStrings::MI_DELTA_ABS_TARE_DYN_E;
        case C::MI_DELTA_DYN:
            return ColumnsStrings::MI_DELTA_DYN;
        case C::MI_DELTA_DYN_E:
            return ColumnsStrings::MI_DELTA_DYN_E;
        case C::MI_TARE_STA:
            return ColumnsStrings::MI_TARE_STA;
        case C::MI_TARE_STA_SCALES:
            return ColumnsStrings::MI_TARE_STA_SCALES;
        case C::MI_TARE_STA_DATETIME:
            return ColumnsStrings::MI_TARE_STA_BDATETIME;
        case C::MI_DELTA_ABS_TARE_STA:
            return ColumnsStrings::MI_DELTA_ABS_TARE_STA;
        case C::MI_DELTA_ABS_TARE_STA_E:
            return ColumnsStrings::MI_DELTA_ABS_TARE_STA_E;
        case C::MI_DELTA_STA:
            return ColumnsStrings::MI_DELTA_STA;
        case C::MI_DELTA_STA_E:
            return ColumnsStrings::MI_DELTA_STA_E;

        case C::MI_3115_LOSS_SUPPLIER:
            return ColumnsStrings::MI_3115_LOSS_SUPPLIER;
        case C::MI_3115_DELTA_SUPPLIER:
            return ColumnsStrings::MI_3115_DELTA_SUPPLIER;
        case C::MI_3115_DELTA_FROM_TABLES:
            return ColumnsStrings::MI_3115_DELTA_FROM_TABLES;
        case C::MI_3115_DELTA_FOR_STATIONS:
            return ColumnsStrings::MI_3115_DELTA_FOR_STATIONS;
        case C::MI_3115_DELTA:
            return ColumnsStrings::MI_3115_DELTA;
        case C::MI_3115_TOLERANCE:
            return ColumnsStrings::MI_3115_TOLERANCE;
        case C::MI_3115_RESULT:
            return ColumnsStrings::MI_3115_RESULT;

        case C::IRON_DATE:
            return ColumnsStrings::DATE;
        case C::IRON_ESPC_RAZL:
            return ColumnsStrings::IRON_ESPC_RAZL;
        case C::IRON_ESPC:
            return ColumnsStrings::IRON_ESPC;
        case C::IRON_RAZL:
            return ColumnsStrings::IRON_RAZL;
        case C::IRON_SHCH:
            return ColumnsStrings::IRON_SHCH;
        case C::IRON_INGOT:
            return ColumnsStrings::IRON_INGOT;

        case C::IRON_CONTROL_SCALES_STA:
            return ColumnsStrings::IRON_CONTROL_SCALES_STA;
        case C::IRON_CONTROL_DATETIME_STA:
            return ColumnsStrings::IRON_CONTROL_DATETIME_STA;
        case C::IRON_CONTROL_SCALES_DYN:
            return ColumnsStrings::IRON_CONTROL_SCALES_DYN;
        case C::IRON_CONTROL_DATETIME_DYN:
            return ColumnsStrings::IRON_CONTROL_DATETIME_DYN;
        case C::IRON_CONTROL_NETTO_STA:
            return ColumnsStrings::IRON_CONTROL_NETTO_STA;
        case C::IRON_CONTROL_NETTO_DYN:
            return ColumnsStrings::IRON_CONTROL_NETTO_DYN;
        case C::IRON_CONTROL_DIFF_DYN_CARR:
            return ColumnsStrings::IRON_CONTROL_DIFF_DYN_CARR;
        case C::IRON_CONTROL_DIFF_DYN_STA:
            return ColumnsStrings::IRON_CONTROL_DIFF_DYN_STA;
        case C::IRON_CONTROL_DIFF_SIDE:
            return ColumnsStrings::IRON_CONTROL_DIFF_SIDE;
        case C::IRON_CONTROL_DIFF_CARRIAGE:
            return ColumnsStrings::IRON_CONTROL_DIFF_CARRIAGE;

        default:
            return $fieldName;
    }
}

#[Pure] function columnTitle(string $fieldName): ?string
{
    switch ($fieldName) {
        case C::IRON_CONTROL_DIFF_SIDE:
            return ColumnsTitleStrings::IRON_CONTROL_DIFF_SIDE;
        case C::IRON_CONTROL_DIFF_CARRIAGE:
            return ColumnsTitleStrings::IRON_CONTROL_DIFF_CARRIAGE;

        default:
            return null;
    }
}

#[Pure] function isFieldLeftAlign(bool $newDesign, string $fieldName): bool
{
    if ($newDesign) {
        switch ($fieldName) {
            case C::TRAIN_NUM:

            case C::SCALE_NUM:
            case C::SCALE_MIN_CAPACITY:
            case C::SCALE_MAX_CAPACITY:
            case C::SCALE_DISCRETENESS:
            case C::IRON_CONTROL_SCALES_STA:
            case C::IRON_CONTROL_SCALES_DYN:

            case C::UNIX_TIME:
            case C::TRAIN_NUMBER:
            case C::CARRYING:
            case C::LOAD_NORM:
            case C::VOLUME:
            case C::TARE:
            case C::BRUTTO:
            case C::NETTO:
            case C::OVERLOAD:
            case C::VAN_COUNT:
            case C::OPERATOR_TAB_NUMBER:
            case C::OPERATOR_SHIFT_NUMBER:
            case C::OPERATOR_SHIFT_SYMBOL:
            case C::RAIL_PATH:
            case C::STATUS:
            case C::SEQUENCE_NUMBER:
            case C::TARE_MANUAL:
            case C::TARE_DYNAMIC:
            case C::TARE_STATIC:
            case C::TARE_TYPE:
            case C::TARE_SCALE_NUMBER:
            case C::DATETIME_TARE:
            case C::BRUTTO_NEAR_SIDE:
            case C::BRUTTO_FAR_SIDE:
            case C::BRUTTO_FIRST_CARRIAGE:
            case C::BRUTTO_SECOND_CARRIAGE:
            case C::SIDE_DIFFERENCE:
            case C::CARRIAGE_DIFFERENCE:
            case C::MASS:
            case C::ACCELERATION:
            case C::CARGO_TYPE_CODE:
            case C::AXIS_COUNT:
            case C::INVOICE_NETTO:
            case C::INVOICE_TARE:
            case C::INVOICE_OVERLOAD:
            case C::LOADING_GROUP:
            case C::TARE_NEAR_SIDE:
            case C::TARE_FAR_SIDE:
            case C::TARE_FIRST_CARRIAGE:
            case C::TARE_SECOND_CARRIAGE:
            case C::UNIT_NUMBER:
            case C::DISCRETENESS:
            case C::COEFFICIENT_P1:
            case C::COEFFICIENT_Q1:
            case C::COEFFICIENT_T1:
            case C::COEFFICIENT_P2:
            case C::COEFFICIENT_Q2:
            case C::COEFFICIENT_T2:
            case C::TEMPERATURE_1:
            case C::TEMPERATURE_2:
            case C::COUNT_ID:
            case C::WEIGH_NAME_CODE:
            case C::PRODUCT_CODE:
            case C::PART_CODE:
            case C::HUMIDITY:

            case C::MI_DELTA_ABS_BRUTTO:
            case C::MI_DELTA_ABS_BRUTTO_E:
            case C::MI_DELTA_ABS_TARE:
            case C::MI_DELTA_ABS_TARE_E:
            case C::MI_DELTA:
            case C::MI_DELTA_E:
            case C::MI_TARE_DYN:
            case C::MI_TARE_DYN_SCALES:
            case C::MI_DELTA_ABS_TARE_DYN:
            case C::MI_DELTA_ABS_TARE_DYN_E:
            case C::MI_DELTA_DYN:
            case C::MI_DELTA_DYN_E:
            case C::MI_TARE_STA:
            case C::MI_TARE_STA_SCALES:
            case C::MI_DELTA_ABS_TARE_STA:
            case C::MI_DELTA_ABS_TARE_STA_E:
            case C::MI_DELTA_STA:
            case C::MI_DELTA_STA_E:

            case C::MI_3115_LOSS_SUPPLIER:
            case C::MI_3115_DELTA_SUPPLIER:
            case C::MI_3115_DELTA_FROM_TABLES:
            case C::MI_3115_DELTA_FOR_STATIONS:
            case C::MI_3115_DELTA:
            case C::MI_3115_TOLERANCE:
            case C::MI_3115_RESULT:

            case C::SCALE_CLASS_DYNAMIC:

            case C::IRON_ESPC_RAZL:
            case C::IRON_ESPC:
            case C::IRON_RAZL:
            case C::IRON_SHCH:
            case C::IRON_INGOT:

            case C::IRON_CONTROL_NETTO_STA:
            case C::IRON_CONTROL_NETTO_DYN:
            case C::IRON_CONTROL_DIFF_DYN_CARR:
            case C::IRON_CONTROL_DIFF_DYN_STA:
            case C::IRON_CONTROL_DIFF_SIDE:
            case C::IRON_CONTROL_DIFF_CARRIAGE:
                return false;

            case C::IRON_DATE:
            case C::DATETIME:
            case C::DATETIME_END:
            case C::VAN_NUMBER:
            case C::VELOCITY:
            case C::OPERATOR:
            case C::DATETIME_OPERATOR:
            case C::VAN_TYPE:
            case C::CARGO_TYPE:
            case C::DATETIME_CARGO:
            case C::COUNTRY:
            case C::DEPART_STATION:
            case C::DEPART_STATION_CODE:
            case C::PURPOSE_STATION:
            case C::PURPOSE_STATION_CODE:
            case C::DATETIME_SHIPMENT:
            case C::INVOICE_NUMBER:
            case C::INVOICE_SUPPLIER:
            case C::INVOICE_RECIPIENT:
            case C::WMODE:
            case C::LOADING_PLACE:
            case C::DATETIME_FAILURE:
            case C::MESSAGE:
            case C::UNIX_TIME_END:
            case C::DATETIME_T:
            case C::OPERATION_TYPE:
            case C::ACCURACY_CLASS:
            case C::VERIFIER:
            case C::COMMENT:

            case C::AUTO_NUMBER:
            case C::DRIVER:

            case C::WEIGH_NAME:
            case C::PRODUCT:
            case C::LEFT_SIDE:

            case C::MI_TARE_DYN_DATETIME:
            case C::MI_TARE_STA_DATETIME:

            case C::SCALE_CLASS_STATIC:

            default:
                return true;
        }
    } else {
        switch ($fieldName) {
            case C::OPERATOR:
            case C::DRIVER:
            case C::MESSAGE:
            case C::COMMENT:
                return true;

            default:
                return false;
        }
    }
}

#[Pure] function getLastDay(int $month, int $year): int
{
    switch ($month) {
        case 4:
        case 6:
        case 9:
        case 11:
            return 30;
        case 2:
            return ($year % 4 == 0 && $year % 100) || $year % 400 == 0 ? 29 : 28;
        default:
            return 31;
    }
}

// used in result.php
/** @noinspection PhpUnused */
#[Pure] function formatDateTime($timestamp): string
{
    return date("d.m.Y H:i", $timestamp);
}

// used in result.php
/** @noinspection PhpUnused */
#[Pure] function formatDate($timestamp): string
{
    return date("d.m.Y", $timestamp);
}

function formatExcelData(string $value): string
{
    $value = strip_tags($value);

    if (strlen($value) == 0) {
        return "";
    } elseif (strcmp($value, "&nbsp;") == 0) {
        return "";
    } else {
        $value = str_replace("&shy;", "", $value);
        $value = str_replace("&nbsp;", Strings::SPACE, $value);
        // Символы > и < удаляются функцией strip_tags (выше),
        // поэтому на страницах вместо них используются &gt; и &lt;
        $value = str_replace("&gt;", ">", $value);
        $value = str_replace("&lt;", "<", $value);

        return '"' . $value . '"';
    }
}

#[Pure] function var_to_bool(mixed $var): bool
{
    $bool = is_string($var) ?
        filter_var($var, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) :
        (bool)$var;
    return $bool === null ? false : $bool;
}

/**
 * Проверяет POST и GET на запрос вывода нового интерфейса.
 * Если запросов нет, используется параметр $default.
 * Если запрошен новый интерфейс, проверяет браузер на совместимость.
 * Если браузер несовместим с новым интерфейсом и по умолчанию новый интерфейс не нужен,
 * но при этом был запрошен через POST или GET,
 * выполняется переход на страницу с сообщением о несовместимом браузере.
 * Если по умолчанию требуется новый интерфейс, но браузер несовместим с ним,
 * выводится старый интерфейс.
 *
 * @param bool $default
 * @return bool
 */
function isNewDesign(bool $default = false): bool
{
    if (isset($_POST[ParamName::NEW_DESIGN])) {
        $newDesign = var_to_bool($_POST[ParamName::NEW_DESIGN]);
    } elseif (isset($_GET[ParamName::NEW_DESIGN])) {
        $newDesign = var_to_bool($_GET[ParamName::NEW_DESIGN]);
    } else {
        $newDesign = $default;
    }

    if ($newDesign) {
        if (CheckBrowser::isCompatibleVersion()) {
            return true;
        }

        if (!$default) {
            header("Location: " . "/incompatible_browser.php");
            die();
        }
    }

    return false;
}

function getPOSTParam(string $param): mixed
{
    if (isset($_POST[(string)$param])) {
        if ($_POST[(string)$param] == "") {
            return null;
        } else {
            return $_POST[(string)$param];
        }
    } else {
        return null;
    }
}

#[Pure] function getParamGETAsInt(string $param, ?int $default = null): ?int
{
    return isset($_GET[$param]) ? (int)$_GET[$param] : $default;
}

#[Pure] function getParamGETAsBool(string $param, ?bool $default = null): ?bool
{
    return isset($_GET[$param]) ? var_to_bool($_GET[$param]) : $default;
}

#[Pure] function getParamGETAsString(string $param, ?string $default = null): ?string
{
    return (isset($_GET[$param]) && ($_GET[$param] != null)) ? urldecode($_GET[$param]) : $default;
}

function isResultTypeCompare(int $resultType): bool
{
    return $resultType == ResultType::COMPARE_DYNAMIC || $resultType == ResultType::COMPARE_STATIC;
}

function isResultTypeCargoList(int $resultType): bool
{
    return
        $resultType == ResultType::CARGO_LIST_DYNAMIC ||
        $resultType == ResultType::CARGO_LIST_STATIC ||
        $resultType == ResultType::CARGO_LIST_AUTO;
}

function getResultHeader(int $resultType): string
{
    switch ($resultType) {
        case ResultType::VAN_DYNAMIC_BRUTTO:
            return S::HEADER_RESULT_VN_DYN_B;
        case ResultType::VAN_DYNAMIC_TARE:
            return S::HEADER_RESULT_VN_DYN_T;

        case ResultType::VAN_STATIC_BRUTTO:
            return S::HEADER_RESULT_VN_STA_B;
        case ResultType::VAN_STATIC_TARE:
            return S::HEADER_RESULT_VN_STA_T;

        case ResultType::TRAIN_DYNAMIC:
            return S::HEADER_RESULT_TR_DYN;
        case ResultType::TRAIN_DYNAMIC_ONE:
            return S::HEADER_RESULT_TR_DYN_ONE;

        case ResultType::AUTO_BRUTTO:
            return S::HEADER_RESULT_AUTO_B;
        case ResultType::AUTO_TARE:
            return S::HEADER_RESULT_AUTO_T;

        case ResultType::KANAT:
            return S::HEADER_RESULT_KANAT;

        case ResultType::DP:
            return S::HEADER_RESULT_DP;
        case ResultType::DP_SUM:
            return S::HEADER_RESULT_DP_SUM;

        case ResultType::CARGO_LIST_DYNAMIC:
        case ResultType::CARGO_LIST_STATIC:
        case ResultType::CARGO_LIST_AUTO:
            return S::HEADER_RESULT_CARGO_LIST;

        case ResultType::COMPARE_DYNAMIC:
        case ResultType::COMPARE_STATIC:
            return S::HEADER_RESULT_COMPARE;

        case ResultType::COEFFS:
            return S::HEADER_COEFF;

        case ResultType::IRON:
            return S::HEADER_IRON;
        case ResultType::IRON_CONTROL:
            return S::HEADER_IRON_CONTROL;

        case ResultType::VANLIST_WEIGHS:
            return S::HEADER_VANLIST_WEIGHS;
        case ResultType::VANLIST_LAST_TARE:
            return S::HEADER_VANLIST_TARE;

        default:
            throw new InvalidArgumentException("Unknown resultType ($resultType)");
    }
}

function boolToString($value): string
{
    return isset($value) ? ($value ? "true" : "false") : "null";
}

/**
 * Выполняет перекодировку строки из latin1 в UTF-8.
 *
 * @param string|null $s
 * @return string|null
 * @see \database\Info::CHARSET
 */
#[Pure] function latin1ToUtf8(?string $s): ?string
{
    if ($s == null) return null;
    $s = iconv('Windows-1251', 'UTF-8', $s);
    return $s;
}

#[Pure] function utf8ToLatin1(?string $s): ?string
{
    if ($s == null) return null;
    $s = iconv('UTF-8', 'Windows-1251', $s);
    return $s;
}

#[Pure] function getCookieAsBool(?string $param): bool
{
    return isset($param) && isset($_COOKIE[$param]) ? var_to_bool($_COOKIE[$param]) : false;
}

function setCookieAsString(string $param, ?string $value)
{
    if (!isset($value)) {
        setcookie($param, null, time() - 1);
        unset($_COOKIE[$param]);

        return;
    }

    setcookie($param, $value);
    $_COOKIE[$param] = $value;
}

function setCookieAsBool(string $param, ?bool $value)
{
    if (!isset($value)) {
        setcookie($param, null, time() - 1);
        unset($_COOKIE[$param]);

        return;
    }

    setcookie($param, boolToString($value));
    $_COOKIE[$param] = boolToString($value);
}

function vanListStringToArray(?string $value): array
{
    $result = array();

    $value .= ';';

    $str = null;
    for ($i = 0, $l = strlen($value); $i < $l; $i++) {
        if ($value[$i] >= '0' && $value[$i] <= '9') {
            $str .= $value[$i];
        } else {
            if ($str != null) $result[] = $str;
            $str = null;
        }
    }

    return $result;
}

function vanListArrayToString(?array $vanList): ?string
{
    if ($vanList == null || count($vanList) == 0) return null;

    for ($i = 0, $c = count($vanList); $i < $c; $i++) {
        $vanList[$i] = "'" . $vanList[$i] . "'";
    }

    return implode(', ', $vanList);
}
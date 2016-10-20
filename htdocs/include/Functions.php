<?php
require_once "MySQLConnection.php";
require_once "Strings.php";
require_once "Constants.php";
require_once "Database.php";

use Database\Columns as C;
use Strings as S;

function concatStrings($s1, $s2, $separator)
{
    if (($s1 == "") && ($s2 == "")) return "";
    elseif ($s1 == "") return $s2;
    elseif ($s2 == "") return $s1;
    else return $s1 . $separator . $s2;
}

class FieldInfo
{
    public $name;
    public $visible;
    public $leftAlign;
}

/**
 * @param mysqli_result $queryResult
 * @param bool $newDesign
 * @param bool $full
 * @param ScaleInfo $scaleInfo
 * @param int $type
 * @return FieldInfo[]
 */
function getFieldsInfo($queryResult, $newDesign, $full, $scaleInfo, $type)
{
    $result = array();

    for ($i = 0; $i < $queryResult->field_count; $i++) {
        $fieldInfo = new FieldInfo();
        $fieldInfo->name = $queryResult->fetch_field_direct($i)->name;
        $fieldInfo->visible = $full || isFieldVisible($fieldInfo->name, $scaleInfo, $type);
        $fieldInfo->leftAlign = $newDesign ?
            isFieldString($fieldInfo->name) :
            isFieldLeftAlign($fieldInfo->name);

        $result[] = $fieldInfo;
    }

    return $result;
}

function formatFieldValue($fieldName, $fieldValue, $full)
{
    if (!empty($fieldValue)) {
        switch ($fieldName) {
            case C::DATETIME:
            case C::DATETIME_END:
            case C::DATETIME_T:
            case C::DATETIME_OPERATOR:
            case C::DATETIME_TARE:
            case C::DATETIME_SHIPMENT:
            case C::DATETIME_FAILURE:
            case C::DATETIME_CARGO:
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
            case C::TRAIN_NUMBER:
                return substr($fieldValue, 0, 16);
            case C::VAN_NUMBER:
                return substr($fieldValue, 0, 8);
            case C::AUTO_NUMBER:
                return substr($fieldValue, 0, 9);
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
                return number_format((double)$fieldValue, 2, ",", "");
            case C::NETTO:
                $s = number_format((double)$fieldValue, 2, ",", "");
                return "<b>" . $s . "</b>";
            case C::OVERLOAD:
            case C::INVOICE_OVERLOAD:
            case C::COMPARE:
                $overload = number_format((double)$fieldValue, 2, ",", "");
                if ($fieldValue > 0) {
                    $overload = "+" . $overload;
                }
                if ($fieldName == C::COMPARE) {
                    $overload = "<b>" . $overload . "</b>";
                }
                return $overload;
            case C::VELOCITY:
                $velocity = number_format(abs($fieldValue), 1, ",", "");
                return $fieldValue > 0 ? $velocity . " >>>" : $velocity = "<<< " . $velocity;
            case C::SCALE_CLASS:
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
            default:
                return $fieldValue;
        }
    } else {
        // IE6 fix.
        return "<span style='zoom: 1;'></span>";
    }
}

/**
 * @param $fieldName
 * @param ScaleInfo $scalesInfo
 * @param int $resultType
 * @return bool
 */
function isFieldVisible($fieldName, $scalesInfo, $resultType)
{
    switch ($fieldName) {
        case C::SCALE_NUM:
            return $scalesInfo->getScaleNum() == Constants::SCALE_NUM_ALL_TRAIN_SCALES;
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
        case C::SCALE_CLASS:
        case C::MESSAGE:

        case C::UNIT_NUMBER:
        case C::DATETIME_T:
        case C::OPERATION_TYPE:
        case C::ACCURACY_CLASS:
        case C::DISCRETENESS:
        case C::COEFFICIENT_P1:
        case C::COEFFICIENT_Q1:
        case C::COEFFICIENT_P2:
        case C::COEFFICIENT_Q2:
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
                $resultType == ResultType::COMPARE_STATIC;
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
        default:
            return false;
    }
}

/**
 * @param $fieldName
 * @param int $scaleType
 * @return string
 */
function columnName($fieldName, $scaleType)
{
    switch ($fieldName) {
        case C::TRAIN_NUM:
            return ColumnsStrings::TRAIN_NUM;
        case C::SCALE_NUM:
            return ColumnsStrings::SCALE_NUM;
        case C::UNIX_TIME:
            return ColumnsStrings::UNIX_TIME;
        case C::DATETIME:
            return ColumnsStrings::DATETIME;
        case C::DATETIME_END:
            return ColumnsStrings::DATETIME_END;
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
        case C::SCALE_CLASS:
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
        case C::COEFFICIENT_P2:
            return ColumnsStrings::COEFFICIENT_P2;
        case C::COEFFICIENT_Q2:
            return ColumnsStrings::COEFFICIENT_Q2;
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
        case C::PRODUCT:
            return ColumnsStrings::PRODUCT;
        case C::LEFT_SIDE:
            return ColumnsStrings::LEFT_SIDE;
        case C::COUNT_ID:
            return ColumnsStrings::COUNT_ID;

        case C::COMPARE:
            return ColumnsStrings::COMPARE;

        default:
            return $fieldName;
    }
}

function isFieldString($fieldName)
{
    switch ($fieldName) {
        case C::TRAIN_NUM:
        case C::SCALE_NUM:
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
        case C::COEFFICIENT_P2:
        case C::COEFFICIENT_Q2:
        case C::TEMPERATURE_1:
        case C::TEMPERATURE_2:
        case C::COUNT_ID:
            return false;

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
        case C::SCALE_CLASS:
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
            return true;

        default:
            return true;
    }
}

function isFieldLeftAlign($fieldName)
{
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

/**
 * @param int $month
 * @param int $year
 * @return int
 */
function getLastDay($month, $year)
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

function formatDateTime($timestamp)
{
    return date("d.m.Y H:i", $timestamp);
}

/**
 * @param string $value
 * @return string
 */
function formatExcelData($value)
{
    $value = strip_tags($value);

    if (strlen($value) == 0) {
        return "";
    } elseif (strcmp($value, "&nbsp;") == 0) {
        return "";
    } else {
        $value = str_replace("&shy;", "", $value);
        $value = str_replace("&nbsp;", " ", $value);
        return '"' . $value . '"';
    }
}

// TODO: обновить после смены версии PHP
if (!function_exists('boolval')) {
    /**
     * @param mixed $val
     * @return bool
     */
    function boolval($val)
    {
        $boolval = is_string($val) ?
            filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) :
            (bool)$val;
        return $boolval === null ? false : $boolval;
    }
}

function isNewDesign($default = false)
{
    return isset($_POST[ParamName::NEW_DESIGN]) ?
        boolval($_POST[ParamName::NEW_DESIGN]) :
        (isset($_GET[ParamName::NEW_DESIGN]) ?
            boolval($_GET[ParamName::NEW_DESIGN]) :
            $default);
}

/**
 * @param string $param
 * @return mixed|null
 */
function getPOSTParam($param)
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

/**
 * @param $param
 * @param int|null $default
 * @return int|null
 */
function getParamGETAsInt($param, $default = null)
{
    return isset($_GET[(string)$param]) ? (int)$_GET[(string)$param] : $default;
}

/**
 * @param string $param
 * @param bool|null $default
 * @return bool|null
 */
function getParamGETAsBool($param, $default = null)
{
    return isset($_GET[(string)$param]) ? boolval($_GET[(string)$param]) : $default;
}

/**
 * @param string $param
 * @param null|string $default
 * @return null|string
 */
function getParamGETAsString($param, $default = null)
{
    return isset($_GET[(string)$param]) ? urldecode($_GET[(string)$param]) : $default;
}

function isResultTypeCompare($resultType)
{
    return $resultType == ResultType::COMPARE_DYNAMIC || $resultType == ResultType::COMPARE_STATIC;
}

function isResultTypeCargoList($resultType)
{
    return
        $resultType == ResultType::CARGO_LIST_DYNAMIC ||
        $resultType == ResultType::CARGO_LIST_STATIC ||
        $resultType == ResultType::CARGO_LIST_AUTO;
}

/**
 * @param int $resultType
 * @return string
 * @see ResultType
 */
function getResultHeader($resultType)
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
        default:
            throw new InvalidArgumentException("Unknown resultType ($resultType)");
    }
}

function boolToString($value)
{
    return isset($value) ? ($value ? "true" : "false") : "null";
}
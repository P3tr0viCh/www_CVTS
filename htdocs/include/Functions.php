<?php /** @noinspection PhpIllegalPsrClassPathInspection */
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

            case C::MI_3115_TOLERANCE:
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
                return match ((int)$fieldValue) {
                    1 => S::TEXT_SCALE_CLASS_DYNAMIC,
                    2 => S::TEXT_SCALE_CLASS_STATIC,
                    default => S::TEXT_SCALE_CLASS_UNKNOWN,
                };

            case C::OPERATION_TYPE:
                return match ((int)$fieldValue) {
                    10 => S::TEXT_OPERATION_TYPE_CALIBRATION_DYNAMIC,
                    20 => S::TEXT_OPERATION_TYPE_CALIBRATION_STATIC,
                    11 => S::TEXT_OPERATION_TYPE_VERIFICATION_DYNAMIC,
                    21 => S::TEXT_OPERATION_TYPE_VERIFICATION_STATIC,
                    40 => S::TEXT_OPERATION_TYPE_MAINTENANCE,
                    50 => S::TEXT_OPERATION_TYPE_REPAIR,
                    default => S::TEXT_OPERATION_TYPE_UNKNOWN,
                };

            case C::TARE_TYPE:
                return match ((int)$fieldValue) {
                    0 => S::TEXT_TARE_TYPE_MANUAL,
                    1 => S::TEXT_TARE_TYPE_DYNAMIC,
                    2 => S::TEXT_TARE_TYPE_STATIC,
                    default => S::TEXT_TARE_TYPE_UNKNOWN,
                };

            case C::LEFT_SIDE:
                return match ((int)$fieldValue) {
                    0 => S::TEXT_SIDE_RIGHT,
                    1 => S::TEXT_SIDE_LEFT,
                    default => S::TEXT_SIDE_UNKNOWN,
                };

            case C::COEFFICIENT_P1:
            case C::COEFFICIENT_P2:
                return num_fmt($fieldValue, 5);
            case C::COEFFICIENT_Q1:
            case C::COEFFICIENT_Q2:
                return num_fmt($fieldValue, 0);
            case C::TEMPERATURE_1:
            case C::TEMPERATURE_2:
            case C::TEMPERATURE_3:
            case C::TEMPERATURE_4:
            case C::TEMPERATURE_5:
            case C::TEMPERATURE_6:
            case C::TEMPERATURE_7:
            case C::TEMPERATURE_8:
            case C::COEFFICIENT_T1:
            case C::COEFFICIENT_T2:
                return num_fmt($fieldValue, 1);

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
                /** @noinspection PhpDuplicateMatchArmBodyInspection */
                return match ((int)$fieldValue) {
                    0 => S::TEXT_SCALE_CLASS_DYNAMIC_0,
                    1 => S::TEXT_SCALE_CLASS_DYNAMIC_1,
                    2 => S::TEXT_SCALE_CLASS_DYNAMIC_2,
                    3 => S::TEXT_SCALE_CLASS_DYNAMIC_3,
                    4 => S::TEXT_SCALE_CLASS_DYNAMIC_4,
                    100, 200, 300 => S::TEXT_SCALE_CLASS_STATIC_0,
                    101, 201, 301 => S::TEXT_SCALE_CLASS_STATIC_1,
                    102, 202, 302 => S::TEXT_SCALE_CLASS_STATIC_2,
                    400 => S::TEXT_SCALE_CLASS_DYNAMIC_3,
                    default => S::TEXT_SIDE_UNKNOWN,
                };

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
                    return match ($fieldName) {
                        C::MI_TARE_DYN, C::MI_TARE_STA,
                        C::MI_DELTA_E, C::MI_DELTA_DYN_E, C::MI_DELTA_STA_E,
                        C::MI_DELTA_ABS_BRUTTO_E, C::MI_DELTA_ABS_TARE_E,
                        C::MI_DELTA_ABS_TARE_DYN_E, C::MI_DELTA_ABS_TARE_STA_E => num_fmt($fieldValue, 2),
                        C::MI_DELTA, C::MI_DELTA_DYN, C::MI_DELTA_STA => num_fmt($fieldValue, 1),
                        C::MI_DELTA_ABS_BRUTTO, C::MI_DELTA_ABS_TARE,
                        C::MI_DELTA_ABS_TARE_DYN, C::MI_DELTA_ABS_TARE_STA => num_fmt($fieldValue, 0),
                        default => $fieldValue,
                    };
                }

            case C::HUMIDITY:
            case C::MI_3115_LOSS_SUPPLIER:
            case C::MI_3115_DELTA_SUPPLIER:
            case C::MI_3115_DELTA_FROM_TABLES:
            case C::MI_3115_DELTA_FOR_STATIONS:
            case C::MI_3115_DELTA:
                return num_fmt($fieldValue, 2) . '%';
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
    return match ($fieldName) {
        C::SCALE_NUM => match ($scalesInfo->getScaleNum()) {
            Constants::SCALE_NUM_ALL_TRAIN_SCALES, Constants::SCALE_NUM_REPORT_VANLIST => true,
            default => false,
        },
        C::DATETIME,
        C::TRAIN_NUMBER,
        C::BRUTTO, C::TARE, C::NETTO,
        C::VAN_COUNT,
        C::OPERATOR, C::PRODUCT, C::LEFT_SIDE,
        C::VAN_NUMBER, C::VAN_TYPE,
        C::AUTO_NUMBER, C::DRIVER, C::DATETIME_TARE,
        C::CARGO_TYPE, C::DATETIME_CARGO,
        C::DATETIME_FAILURE,
        C::WMODE, C::MESSAGE, C::UNIT_NUMBER,
        C::DATETIME_T,
        C::OPERATION_TYPE, C::ACCURACY_CLASS, C::DISCRETENESS,
        C::COEFFICIENT_P1, C::COEFFICIENT_Q1, C::COEFFICIENT_T1,
        C::COEFFICIENT_P2, C::COEFFICIENT_Q2, C::COEFFICIENT_T2,
        C::VERIFIER, C::COMMENT,
        C::INVOICE_NUMBER, C::INVOICE_SUPPLIER, C::INVOICE_RECIPIENT,
        C::SIDE_DIFFERENCE, C::CARRIAGE_DIFFERENCE,
        C::MI_DELTA_ABS_BRUTTO, C::MI_DELTA_ABS_TARE, C::MI_DELTA,
        C::MI_TARE_DYN, C::MI_TARE_DYN_SCALES, C::MI_TARE_DYN_DATETIME, C::MI_DELTA_ABS_TARE_DYN, C::MI_DELTA_DYN,
        C::MI_TARE_STA, C::MI_TARE_STA_SCALES, C::MI_TARE_STA_DATETIME, C::MI_DELTA_ABS_TARE_STA, C::MI_DELTA_STA,
        C::MI_3115_LOSS_SUPPLIER, C::MI_3115_DELTA_SUPPLIER, C::MI_3115_DELTA_FROM_TABLES,
        C::MI_3115_DELTA_FOR_STATIONS, C::MI_3115_DELTA, C::MI_3115_TOLERANCE,
        C::MI_3115_RESULT,
        C::IRON_DATE, C::IRON_ESPC_RAZL, C::IRON_ESPC, C::IRON_RAZL, C::IRON_SHCH, C::IRON_INGOT,
        C::IRON_CONTROL_SCALES_STA, C::IRON_CONTROL_DATETIME_STA,
        C::IRON_CONTROL_SCALES_DYN, C::IRON_CONTROL_DATETIME_DYN,
        C::IRON_CONTROL_NETTO_STA, C::IRON_CONTROL_NETTO_DYN,
        C::IRON_CONTROL_DIFF_DYN_CARR, C::IRON_CONTROL_DIFF_DYN_STA,
        C::IRON_CONTROL_DIFF_SIDE, C::IRON_CONTROL_DIFF_CARRIAGE => true,
        C::OVERLOAD, C::CARRYING => $scalesInfo->getType() == ScaleType::DEFAULT_TYPE ||
            $resultType == ResultType::COMPARE_DYNAMIC ||
            $resultType == ResultType::COMPARE_STATIC ||
            $resultType == ResultType::IRON_CONTROL,
        C::DEPART_STATION, C::PURPOSE_STATION => $scalesInfo->getType() == ScaleType::DEFAULT_TYPE,
        C::INVOICE_NETTO, C::INVOICE_TARE, C::INVOICE_OVERLOAD => ($scalesInfo->getType() == ScaleType::WMR) ||
            ($scalesInfo->getType() == ScaleType::AUTO),
        C::SEQUENCE_NUMBER => ($resultType != ResultType::TRAIN_DYNAMIC) &&
            ($resultType != ResultType::KANAT),
        C::DATETIME_END => $resultType == ResultType::COEFFS,
        default => false,
    };
}

#[Pure] function columnName(string $fieldName, int $scaleType, int $resultType = null): string
{
    return match ($fieldName) {
        C::TRAIN_NUM => ColumnsStrings::TRAIN_NUM,
        C::SCALE_NUM => ColumnsStrings::SCALE_NUM,
        C::SCALE_MIN_CAPACITY => ColumnsStrings::SCALE_MIN_CAPACITY,
        C::SCALE_MAX_CAPACITY => ColumnsStrings::SCALE_MAX_CAPACITY,
        C::SCALE_DISCRETENESS => ColumnsStrings::SCALE_DISCRETENESS,
        C::UNIX_TIME => ColumnsStrings::UNIX_TIME,
        C::DATETIME => ColumnsStrings::DATETIME,
        C::DATETIME_END => $resultType == ResultType::COEFFS ? ColumnsStrings::DATETIME : ColumnsStrings::DATETIME_END,
        C::TRAIN_NUMBER => ColumnsStrings::TRAIN_NUMBER,
        C::CARRYING => ColumnsStrings::CARRYING,
        C::LOAD_NORM => ColumnsStrings::LOAD_NORM,
        C::VOLUME => ColumnsStrings::VOLUME,
        C::TARE => $scaleType == ScaleType::WMR ? ColumnsStrings::TARE_WMR : ColumnsStrings::TARE,
        C::BRUTTO => ColumnsStrings::BRUTTO,
        C::NETTO => ColumnsStrings::NETTO,
        C::OVERLOAD => ColumnsStrings::OVERLOAD,
        C::VAN_COUNT => ColumnsStrings::VAN_COUNT,
        C::VELOCITY => ColumnsStrings::VELOCITY,
        C::OPERATOR => ColumnsStrings::OPERATOR,
        C::OPERATOR_TAB_NUMBER => ColumnsStrings::OPERATOR_TAB_NUMBER,
        C::OPERATOR_SHIFT_NUMBER => ColumnsStrings::OPERATOR_SHIFT_NUMBER,
        C::OPERATOR_SHIFT_SYMBOL => ColumnsStrings::OPERATOR_SHIFT_SYMBOL,
        C::DATETIME_OPERATOR => ColumnsStrings::DATETIME_OPERATOR,
        C::RAIL_PATH => ColumnsStrings::RAIL_PATH,
        C::STATUS => ColumnsStrings::STATUS,
        C::SEQUENCE_NUMBER => ColumnsStrings::SEQUENCE_NUMBER,
        C::VAN_NUMBER => ColumnsStrings::VAN_NUMBER,
        C::VAN_TYPE => ColumnsStrings::VAN_TYPE,
        C::TARE_MANUAL => ColumnsStrings::TARE_MANUAL,
        C::TARE_DYNAMIC => ColumnsStrings::TARE_DYNAMIC,
        C::TARE_STATIC => ColumnsStrings::TARE_STATIC,
        C::TARE_TYPE => ColumnsStrings::TARE_TYPE,
        C::TARE_SCALE_NUMBER => ColumnsStrings::TARE_SCALE_NUMBER,
        C::DATETIME_TARE => ColumnsStrings::DATETIME_TARE,
        C::BRUTTO_NEAR_SIDE => ColumnsStrings::BRUTTO_NEAR_SIDE,
        C::BRUTTO_FAR_SIDE => ColumnsStrings::BRUTTO_FAR_SIDE,
        C::BRUTTO_FIRST_CARRIAGE => ColumnsStrings::BRUTTO_FIRST_CARRIAGE,
        C::BRUTTO_SECOND_CARRIAGE => ColumnsStrings::BRUTTO_SECOND_CARRIAGE,
        C::SIDE_DIFFERENCE => ColumnsStrings::SIDE_DIFFERENCE,
        C::CARRIAGE_DIFFERENCE => ColumnsStrings::CARRIAGE_DIFFERENCE,
        C::MASS => ColumnsStrings::MASS,
        C::ACCELERATION => ColumnsStrings::ACCELERATION,
        C::CARGO_TYPE => ColumnsStrings::CARGO_TYPE,
        C::CARGO_TYPE_CODE => ColumnsStrings::CARGO_TYPE_CODE,
        C::DATETIME_CARGO => ColumnsStrings::DATETIME_CARGO,
        C::AXIS_COUNT => ColumnsStrings::AXIS_COUNT,
        C::COUNTRY => ColumnsStrings::COUNTRY,
        C::DEPART_STATION => ColumnsStrings::DEPART_STATION,
        C::DEPART_STATION_CODE => ColumnsStrings::DEPART_STATION_CODE,
        C::PURPOSE_STATION => ColumnsStrings::PURPOSE_STATION,
        C::PURPOSE_STATION_CODE => ColumnsStrings::PURPOSE_STATION_CODE,
        C::DATETIME_SHIPMENT => ColumnsStrings::DATETIME_SHIPMENT,
        C::INVOICE_NUMBER => $scaleType == ScaleType::WMR ? ColumnsStrings::INVOICE_NUMBER_WMR : ColumnsStrings::INVOICE_NUMBER,
        C::INVOICE_SUPPLIER => $scaleType == ScaleType::WMR ? ColumnsStrings::INVOICE_SUPPLIER_WMR : ColumnsStrings::INVOICE_SUPPLIER,
        C::INVOICE_RECIPIENT => ColumnsStrings::INVOICE_RECIPIENT,
        C::INVOICE_NETTO => $scaleType == ScaleType::WMR ? ColumnsStrings::INVOICE_NETTO_WMR : ColumnsStrings::INVOICE_NETTO,
        C::INVOICE_TARE => $scaleType == ScaleType::WMR ? ColumnsStrings::INVOICE_TARE_WMR : ColumnsStrings::INVOICE_TARE,
        C::INVOICE_OVERLOAD => $scaleType == ScaleType::WMR ? ColumnsStrings::INVOICE_OVERLOAD_WMR : ColumnsStrings::INVOICE_OVERLOAD,
        C::LOADING_GROUP => ColumnsStrings::LOADING_GROUP,
        C::LOADING_PLACE => ColumnsStrings::LOADING_PLACE,
        C::TARE_NEAR_SIDE => ColumnsStrings::TARE_NEAR_SIDE,
        C::TARE_FAR_SIDE => ColumnsStrings::TARE_FAR_SIDE,
        C::TARE_FIRST_CARRIAGE => ColumnsStrings::TARE_FIRST_CARRIAGE,
        C::TARE_SECOND_CARRIAGE => ColumnsStrings::TARE_SECOND_CARRIAGE,
        C::DATETIME_FAILURE => ColumnsStrings::DATETIME_FAILURE,
        C::WMODE => ColumnsStrings::SCALE_CLASS,
        C::MESSAGE => ColumnsStrings::MESSAGE,
        C::UNIX_TIME_END => ColumnsStrings::UNIX_TIME_END,
        C::UNIT_NUMBER => ColumnsStrings::UNIT_NUMBER,
        C::DATETIME_T => ColumnsStrings::DATETIME_T,
        C::OPERATION_TYPE => ColumnsStrings::OPERATION_TYPE,
        C::ACCURACY_CLASS => ColumnsStrings::ACCURACY_CLASS,
        C::DISCRETENESS => ColumnsStrings::DISCRETENESS,
        C::COEFFICIENT_P1 => ColumnsStrings::COEFFICIENT_P1,
        C::COEFFICIENT_Q1 => ColumnsStrings::COEFFICIENT_Q1,
        C::COEFFICIENT_T1 => ColumnsStrings::COEFFICIENT_T1,
        C::COEFFICIENT_P2 => ColumnsStrings::COEFFICIENT_P2,
        C::COEFFICIENT_Q2 => ColumnsStrings::COEFFICIENT_Q2,
        C::COEFFICIENT_T2 => ColumnsStrings::COEFFICIENT_T2,
        C::TEMPERATURE_1 => ColumnsStrings::TEMPERATURE_1,
        C::TEMPERATURE_2 => ColumnsStrings::TEMPERATURE_2,
        C::TEMPERATURE_3 => ColumnsStrings::TEMPERATURE_3,
        C::TEMPERATURE_4 => ColumnsStrings::TEMPERATURE_4,
        C::TEMPERATURE_5 => ColumnsStrings::TEMPERATURE_5,
        C::TEMPERATURE_6 => ColumnsStrings::TEMPERATURE_6,
        C::TEMPERATURE_7 => ColumnsStrings::TEMPERATURE_7,
        C::TEMPERATURE_8 => ColumnsStrings::TEMPERATURE_8,
        C::VERIFIER => ColumnsStrings::VERIFIER,
        C::COMMENT => ColumnsStrings::COMMENT,
        C::AUTO_NUMBER => ColumnsStrings::AUTO_NUMBER,
        C::DRIVER => ColumnsStrings::DRIVER,
        C::WEIGH_NAME => ColumnsStrings::WEIGH_NAME,
        C::WEIGH_NAME_CODE => ColumnsStrings::WEIGH_NAME_CODE,
        C::PRODUCT => ColumnsStrings::PRODUCT,
        C::PRODUCT_CODE => ColumnsStrings::PRODUCT_CODE,
        C::LEFT_SIDE => ColumnsStrings::LEFT_SIDE,
        C::PART_CODE => ColumnsStrings::PART_CODE,
        C::HUMIDITY => ColumnsStrings::HUMIDITY,
        C::COUNT_ID => ColumnsStrings::COUNT_ID,
        C::COMPARE => ColumnsStrings::COMPARE,
        C::MI_DELTA_ABS_BRUTTO => ColumnsStrings::MI_DELTA_ABS_BRUTTO,
        C::MI_DELTA_ABS_BRUTTO_E => ColumnsStrings::MI_DELTA_ABS_BRUTTO_E,
        C::MI_DELTA_ABS_TARE => ColumnsStrings::MI_DELTA_ABS_TARE,
        C::MI_DELTA_ABS_TARE_E => ColumnsStrings::MI_DELTA_ABS_TARE_E,
        C::MI_DELTA => ColumnsStrings::MI_DELTA,
        C::MI_DELTA_E => ColumnsStrings::MI_DELTA_E,
        C::MI_TARE_DYN => ColumnsStrings::MI_TARE_DYN,
        C::MI_TARE_DYN_SCALES => ColumnsStrings::MI_TARE_DYN_SCALES,
        C::MI_TARE_DYN_DATETIME => ColumnsStrings::MI_TARE_DYN_BDATETIME,
        C::MI_DELTA_ABS_TARE_DYN => ColumnsStrings::MI_DELTA_ABS_TARE_DYN,
        C::MI_DELTA_ABS_TARE_DYN_E => ColumnsStrings::MI_DELTA_ABS_TARE_DYN_E,
        C::MI_DELTA_DYN => ColumnsStrings::MI_DELTA_DYN,
        C::MI_DELTA_DYN_E => ColumnsStrings::MI_DELTA_DYN_E,
        C::MI_TARE_STA => ColumnsStrings::MI_TARE_STA,
        C::MI_TARE_STA_SCALES => ColumnsStrings::MI_TARE_STA_SCALES,
        C::MI_TARE_STA_DATETIME => ColumnsStrings::MI_TARE_STA_BDATETIME,
        C::MI_DELTA_ABS_TARE_STA => ColumnsStrings::MI_DELTA_ABS_TARE_STA,
        C::MI_DELTA_ABS_TARE_STA_E => ColumnsStrings::MI_DELTA_ABS_TARE_STA_E,
        C::MI_DELTA_STA => ColumnsStrings::MI_DELTA_STA,
        C::MI_DELTA_STA_E => ColumnsStrings::MI_DELTA_STA_E,
        C::MI_3115_LOSS_SUPPLIER => ColumnsStrings::MI_3115_LOSS_SUPPLIER,
        C::MI_3115_DELTA_SUPPLIER => ColumnsStrings::MI_3115_DELTA_SUPPLIER,
        C::MI_3115_DELTA_FROM_TABLES => ColumnsStrings::MI_3115_DELTA_FROM_TABLES,
        C::MI_3115_DELTA_FOR_STATIONS => ColumnsStrings::MI_3115_DELTA_FOR_STATIONS,
        C::MI_3115_DELTA => ColumnsStrings::MI_3115_DELTA,
        C::MI_3115_TOLERANCE => ColumnsStrings::MI_3115_TOLERANCE,
        C::MI_3115_RESULT => ColumnsStrings::MI_3115_RESULT,
        C::IRON_DATE => ColumnsStrings::DATE,
        C::IRON_ESPC_RAZL => ColumnsStrings::IRON_ESPC_RAZL,
        C::IRON_ESPC => ColumnsStrings::IRON_ESPC,
        C::IRON_RAZL => ColumnsStrings::IRON_RAZL,
        C::IRON_SHCH => ColumnsStrings::IRON_SHCH,
        C::IRON_INGOT => ColumnsStrings::IRON_INGOT,
        C::IRON_CONTROL_SCALES_STA => ColumnsStrings::IRON_CONTROL_SCALES_STA,
        C::IRON_CONTROL_DATETIME_STA => ColumnsStrings::IRON_CONTROL_DATETIME_STA,
        C::IRON_CONTROL_SCALES_DYN => ColumnsStrings::IRON_CONTROL_SCALES_DYN,
        C::IRON_CONTROL_DATETIME_DYN => ColumnsStrings::IRON_CONTROL_DATETIME_DYN,
        C::IRON_CONTROL_NETTO_STA => ColumnsStrings::IRON_CONTROL_NETTO_STA,
        C::IRON_CONTROL_NETTO_DYN => ColumnsStrings::IRON_CONTROL_NETTO_DYN,
        C::IRON_CONTROL_DIFF_DYN_CARR => ColumnsStrings::IRON_CONTROL_DIFF_DYN_CARR,
        C::IRON_CONTROL_DIFF_DYN_STA => ColumnsStrings::IRON_CONTROL_DIFF_DYN_STA,
        C::IRON_CONTROL_DIFF_SIDE => ColumnsStrings::IRON_CONTROL_DIFF_SIDE,
        C::IRON_CONTROL_DIFF_CARRIAGE => ColumnsStrings::IRON_CONTROL_DIFF_CARRIAGE,
        default => $fieldName,
    };
}

#[Pure] function columnTitle(string $fieldName): ?string
{
    return match ($fieldName) {
        C::IRON_CONTROL_DIFF_SIDE => ColumnsTitleStrings::IRON_CONTROL_DIFF_SIDE,
        C::IRON_CONTROL_DIFF_CARRIAGE => ColumnsTitleStrings::IRON_CONTROL_DIFF_CARRIAGE,
        default => null,
    };
}

#[Pure] function isFieldLeftAlign(bool $newDesign, string $fieldName): bool
{
    if ($newDesign) {
        return match ($fieldName) {
            C::TRAIN_NUM, C::SCALE_NUM,
            C::SCALE_MIN_CAPACITY, C::SCALE_MAX_CAPACITY,
            C::SCALE_DISCRETENESS,
            C::IRON_CONTROL_SCALES_STA, C::IRON_CONTROL_SCALES_DYN,
            C::UNIX_TIME, C::TRAIN_NUMBER,
            C::CARRYING, C::LOAD_NORM, C::VOLUME,
            C::TARE, C::BRUTTO, C::NETTO, C::OVERLOAD, C::VAN_COUNT,
            C::OPERATOR_TAB_NUMBER, C::OPERATOR_SHIFT_NUMBER, C::OPERATOR_SHIFT_SYMBOL,
            C::RAIL_PATH, C::STATUS, C::SEQUENCE_NUMBER,
            C::TARE_MANUAL, C::TARE_DYNAMIC, C::TARE_STATIC, C::TARE_TYPE,
            C::TARE_SCALE_NUMBER, C::DATETIME_TARE,
            C::BRUTTO_NEAR_SIDE, C::BRUTTO_FAR_SIDE,
            C::BRUTTO_FIRST_CARRIAGE, C::BRUTTO_SECOND_CARRIAGE,
            C::SIDE_DIFFERENCE, C::CARRIAGE_DIFFERENCE,
            C::TARE_FIRST_CARRIAGE, C::TARE_SECOND_CARRIAGE,
            C::TARE_NEAR_SIDE, C::TARE_FAR_SIDE,
            C::MASS, C::ACCELERATION, C::CARGO_TYPE_CODE, C::AXIS_COUNT,
            C::INVOICE_NETTO, C::INVOICE_TARE, C::INVOICE_OVERLOAD,
            C::LOADING_GROUP,
            C::UNIT_NUMBER, C::DISCRETENESS,
            C::COEFFICIENT_P1, C::COEFFICIENT_Q1, C::COEFFICIENT_T1,
            C::COEFFICIENT_P2, C::COEFFICIENT_Q2, C::COEFFICIENT_T2,
            C::TEMPERATURE_1, C::TEMPERATURE_2,
            C::ID,
            C::COUNT_ID,
            C::WEIGH_NAME_CODE, C::PRODUCT_CODE, C::PART_CODE,
            C::HUMIDITY,
            C::MI_DELTA_ABS_BRUTTO, C::MI_DELTA_ABS_BRUTTO_E, C::MI_DELTA_ABS_TARE,
            C::MI_DELTA_ABS_TARE_E, C::MI_DELTA, C::MI_DELTA_E, C::MI_TARE_DYN,
            C::MI_TARE_DYN_SCALES, C::MI_DELTA_ABS_TARE_DYN, C::MI_DELTA_ABS_TARE_DYN_E,
            C::MI_DELTA_DYN, C::MI_DELTA_DYN_E, C::MI_TARE_STA, C::MI_TARE_STA_SCALES,
            C::MI_DELTA_ABS_TARE_STA, C::MI_DELTA_ABS_TARE_STA_E, C::MI_DELTA_STA, C::MI_DELTA_STA_E,
            C::MI_3115_LOSS_SUPPLIER, C::MI_3115_DELTA_SUPPLIER,
            C::MI_3115_DELTA_FROM_TABLES, C::MI_3115_DELTA_FOR_STATIONS,
            C::MI_3115_DELTA, C::MI_3115_TOLERANCE, C::MI_3115_RESULT,
            C::SCALE_CLASS_DYNAMIC,
            C::IRON_ESPC_RAZL, C::IRON_ESPC, C::IRON_RAZL, C::IRON_SHCH, C::IRON_INGOT,
            C::IRON_CONTROL_NETTO_STA, C::IRON_CONTROL_NETTO_DYN,
            C::IRON_CONTROL_DIFF_DYN_CARR, C::IRON_CONTROL_DIFF_DYN_STA,
            C::IRON_CONTROL_DIFF_SIDE, C::IRON_CONTROL_DIFF_CARRIAGE => false,
            default => true,
        };
    } else {
        return match ($fieldName) {
            C::OPERATOR, C::DRIVER, C::MESSAGE, C::COMMENT => true,
            default => false,
        };
    }
}

#[Pure] function getLastDay(int $month, int $year): int
{
    return match ($month) {
        4, 6, 9, 11 => 30,
        2 => ($year % 4 == 0 && $year % 100) || $year % 400 == 0 ? 29 : 28,
        default => 31,
    };
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

#[Pure] function isResultTypeCompare(int $resultType): bool
{
    return $resultType == ResultType::COMPARE_DYNAMIC || $resultType == ResultType::COMPARE_STATIC;
}

#[Pure] function isResultTypeCargoList(int $resultType): bool
{
    return
        $resultType == ResultType::CARGO_LIST_DYNAMIC ||
        $resultType == ResultType::CARGO_LIST_STATIC ||
        $resultType == ResultType::CARGO_LIST_AUTO;
}

#[Pure] function getResultHeader(int $resultType): string
{
    return match ($resultType) {
        ResultType::VAN_DYNAMIC_BRUTTO => S::HEADER_RESULT_VN_DYN_B,
        ResultType::VAN_DYNAMIC_TARE => S::HEADER_RESULT_VN_DYN_T,
        ResultType::VAN_STATIC_BRUTTO => S::HEADER_RESULT_VN_STA_B,
        ResultType::VAN_STATIC_TARE => S::HEADER_RESULT_VN_STA_T,
        ResultType::TRAIN_DYNAMIC => S::HEADER_RESULT_TR_DYN,
        ResultType::TRAIN_DYNAMIC_ONE => S::HEADER_RESULT_TR_DYN_ONE,
        ResultType::AUTO_BRUTTO => S::HEADER_RESULT_AUTO_B,
        ResultType::AUTO_TARE => S::HEADER_RESULT_AUTO_T,
        ResultType::KANAT => S::HEADER_RESULT_KANAT,
        ResultType::DP => S::HEADER_RESULT_DP,
        ResultType::DP_SUM => S::HEADER_RESULT_DP_SUM,
        ResultType::CARGO_LIST_DYNAMIC, ResultType::CARGO_LIST_STATIC, ResultType::CARGO_LIST_AUTO => S::HEADER_RESULT_CARGO_LIST,
        ResultType::COMPARE_DYNAMIC, ResultType::COMPARE_STATIC => S::HEADER_RESULT_COMPARE,
        ResultType::COEFFS => S::HEADER_COEFF,
        ResultType::IRON => S::HEADER_IRON,
        ResultType::IRON_CONTROL => S::HEADER_IRON_CONTROL,
        ResultType::VANLIST_WEIGHS => S::HEADER_VANLIST_WEIGHS,
        ResultType::VANLIST_LAST_TARE => S::HEADER_VANLIST_TARE,
        default => throw new InvalidArgumentException("Unknown resultType ($resultType)"),
    };
}

#[Pure] function boolToString($value): string
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

function deleteCookie(string $param)
{
    setcookie($param, null, time() - 1);
    unset($_COOKIE[$param]);
}

function setCookieAsString(string $param, ?string $value)
{
    if (!isset($value)) {
        deleteCookie($param);
        return;
    }

    setcookie($param, $value);
    $_COOKIE[$param] = $value;
}

function setCookieAsBool(string $param, ?bool $value)
{
    if (!isset($value)) {
        deleteCookie($param);
        return;
    }

    setCookieAsString($param, boolToString($value));
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
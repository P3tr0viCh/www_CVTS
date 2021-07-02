<?php /** @noinspection PhpIllegalPsrClassPathInspection */
require_once "MySQLConnection.php";
require_once "Strings.php";
require_once "Constants.php";
require_once "ParamName.php";

use JetBrains\PhpStorm\Pure;
use database\Columns as C;
use ColumnsStrings as CS;
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
        $fieldInfo->visible = isFieldVisible($fieldInfo->name, $scaleInfo, $type, $full);
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
            case C::DATETIME_SENSORS_INFO:

            case C::MI_TARE_DYN_DATETIME:
            case C::MI_TARE_STA_DATETIME:

            case C::IRON_CONTROL_DATETIME_STA:
            case C::IRON_CONTROL_DATETIME_DYN:
            case C::SLAG_CONTROL_DATETIME_STA:
            case C::SLAG_CONTROL_DATETIME_DYN:
                if ($fieldValue == "0000-00-00 00:00:00" ||
                    $fieldValue == "1899-12-30 00:00:00") {
                    return S::TEXT_TABLE_CELL_EMPTY;
                }

                $dateTime = mysqlDateTimeToArray($fieldValue);

                $d = $dateTime['day'] . "." . $dateTime['month'] . "." . $dateTime['year'];
                $d .= "&nbsp;";
                $d .= $dateTime['hour'] . ":" . $dateTime['minute'];
                if ($full) {
                    $d .= ":" . $dateTime['second'];
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
            case C::SLAG_CONTROL_NETTO_STA:
            case C::SLAG_CONTROL_NETTO_DYN:
            case C::SLAG_CONTROL_DIFF_DYN_CARR:
            case C::SLAG_CONTROL_DIFF_SIDE:
            case C::SLAG_CONTROL_DIFF_CARRIAGE:
                return num_fmt($fieldValue, 2);

            case C::NETTO:
            case C::IRON_CONTROL_DIFF_DYN_STA:
            case C::SLAG_CONTROL_DIFF_DYN_STA:
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
            case C::SENSOR_T1:
            case C::SENSOR_T2:
            case C::SENSOR_T3:
            case C::SENSOR_T4:
            case C::SENSOR_T5:
            case C::SENSOR_T6:
            case C::SENSOR_T7:
            case C::SENSOR_T8:
                return num_fmt($fieldValue, 1);
            case C::SENSORS_INFO_TYPE:
                return match ((int)$fieldValue) {
                    0 => S::TEXT_SENSORS_INFO_STATUS,
                    1 => S::TEXT_SENSORS_INFO_ZEROS_CURRENT,
                    2 => S::TEXT_SENSORS_INFO_ZEROS_INITIAL,
                    default => $fieldValue
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

#[Pure] function isFieldVisible(string $fieldName, ScaleInfo $scalesInfo, int $resultType, bool $full): bool
{
    return $full ?
        match ($fieldName) {
            C::TAG2, C::UTIME,
            C::RESERV1, C::RESERV2, C::RESERV3, C::RESERV4,
            C::SENSORS_INIT => false,
            default => true,
        } :
        match ($fieldName) {
            C::SCALE_NUM => match ($scalesInfo->getScaleNum()) {
                Constants::SCALE_NUM_ALL_TRAIN_SCALES,
                Constants::SCALE_NUM_REPORT_VANLIST,
                Constants::SCALE_NUM_REPORT_SENSORS_INFO => true,
                default => false,
            },
            C::SCALE_PLACE,
            C::DATETIME,
            C::DATETIME_SENSORS_INFO,
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
            C::IRON_CONTROL_SCALES_STA,
            C::IRON_CONTROL_SCALES_DYN,
            C::IRON_CONTROL_DATETIME_STA,
            C::IRON_CONTROL_DATETIME_DYN,
            C::IRON_CONTROL_NETTO_STA, C::IRON_CONTROL_NETTO_DYN,
            C::IRON_CONTROL_DIFF_DYN_CARR, C::IRON_CONTROL_DIFF_DYN_STA,
            C::IRON_CONTROL_DIFF_SIDE, C::IRON_CONTROL_DIFF_CARRIAGE,
            C::SLAG_CONTROL_SCALES_STA,
            C::SLAG_CONTROL_SCALES_DYN,
            C::SLAG_CONTROL_DATETIME_STA,
            C::SLAG_CONTROL_DATETIME_DYN,
            C::SLAG_CONTROL_NETTO_STA, C::SLAG_CONTROL_NETTO_DYN,
            C::SLAG_CONTROL_DIFF_DYN_CARR, C::SLAG_CONTROL_DIFF_DYN_STA,
            C::SLAG_CONTROL_DIFF_SIDE, C::SLAG_CONTROL_DIFF_CARRIAGE,
            C::SENSORS_INFO_TYPE,
            C::SENSOR_M1, C::SENSOR_M2, C::SENSOR_M3, C::SENSOR_M4,
            C::SENSOR_M5, C::SENSOR_M6, C::SENSOR_M7, C::SENSOR_M8,
            C::SENSOR_M9, C::SENSOR_M10, C::SENSOR_M11, C::SENSOR_M12,
            C::SENSOR_M13, C::SENSOR_M14, C::SENSOR_M15, C::SENSOR_M16,
            C::SENSOR_T1, C::SENSOR_T2, C::SENSOR_T3, C::SENSOR_T4,
            C::SENSOR_T5, C::SENSOR_T6, C::SENSOR_T7, C::SENSOR_T8 => true,

            C::OVERLOAD, C::CARRYING => $scalesInfo->getType() == ScaleType::DEFAULT_TYPE ||
                $resultType == ResultType::COMPARE_DYNAMIC ||
                $resultType == ResultType::COMPARE_STATIC ||
                $resultType == ResultType::IRON_CONTROL ||
                $resultType == ResultType::SLAG_CONTROL,
            C::DEPART_STATION, C::PURPOSE_STATION => $scalesInfo->getType() == ScaleType::DEFAULT_TYPE,
            C::INVOICE_NETTO, C::INVOICE_TARE, C::INVOICE_OVERLOAD => ($scalesInfo->getType() == ScaleType::WMR) ||
                ($scalesInfo->getType() == ScaleType::AUTO),
            C::SEQUENCE_NUMBER => ($resultType != ResultType::TRAIN_DYNAMIC) &&
                ($resultType != ResultType::KANAT),
            C::DATETIME_END => $resultType == ResultType::COEFFS,

            default => false,
        };
}

#[Pure] function columnName(string $fieldName, int $scaleType = null, int $resultType = null): string
{
    return match ($fieldName) {
        C::TRAIN_NUM => CS::TRAIN_NUM,
        C::SCALE_NUM => CS::SCALE_NUM,
        C::SCALE_PLACE => CS::SCALE_PLACE,
        C::SCALE_MIN_CAPACITY => CS::SCALE_MIN_CAPACITY,
        C::SCALE_MAX_CAPACITY => CS::SCALE_MAX_CAPACITY,
        C::SCALE_DISCRETENESS => CS::SCALE_DISCRETENESS,
        C::UNIX_TIME => CS::UNIX_TIME,
        C::DATETIME,
        C::DATETIME_SENSORS_INFO => CS::DATETIME,
        C::DATETIME_END => $resultType == ResultType::COEFFS ? CS::DATETIME : CS::DATETIME_END,
        C::TRAIN_NUMBER => CS::TRAIN_NUMBER,
        C::CARRYING => CS::CARRYING,
        C::LOAD_NORM => CS::LOAD_NORM,
        C::VOLUME => CS::VOLUME,
        C::TARE => $scaleType == ScaleType::WMR ? CS::TARE_WMR : CS::TARE,
        C::BRUTTO => CS::BRUTTO,
        C::NETTO => CS::NETTO,
        C::OVERLOAD => CS::OVERLOAD,
        C::VAN_COUNT => CS::VAN_COUNT,
        C::VELOCITY => CS::VELOCITY,
        C::OPERATOR => CS::OPERATOR,
        C::OPERATOR_TAB_NUMBER => CS::OPERATOR_TAB_NUMBER,
        C::OPERATOR_SHIFT_NUMBER => CS::OPERATOR_SHIFT_NUMBER,
        C::OPERATOR_SHIFT_SYMBOL => CS::OPERATOR_SHIFT_SYMBOL,
        C::DATETIME_OPERATOR => CS::DATETIME_OPERATOR,
        C::RAIL_PATH => CS::RAIL_PATH,
        C::STATUS => CS::STATUS,
        C::SEQUENCE_NUMBER => CS::SEQUENCE_NUMBER,
        C::VAN_NUMBER => CS::VAN_NUMBER,
        C::VAN_TYPE => CS::VAN_TYPE,
        C::TARE_MANUAL => CS::TARE_MANUAL,
        C::TARE_DYNAMIC => CS::TARE_DYNAMIC,
        C::TARE_STATIC => CS::TARE_STATIC,
        C::TARE_TYPE => CS::TARE_TYPE,
        C::TARE_SCALE_NUMBER => CS::TARE_SCALE_NUMBER,
        C::DATETIME_TARE => CS::DATETIME_TARE,
        C::BRUTTO_NEAR_SIDE => CS::BRUTTO_NEAR_SIDE,
        C::BRUTTO_FAR_SIDE => CS::BRUTTO_FAR_SIDE,
        C::BRUTTO_FIRST_CARRIAGE => CS::BRUTTO_FIRST_CARRIAGE,
        C::BRUTTO_SECOND_CARRIAGE => CS::BRUTTO_SECOND_CARRIAGE,
        C::SIDE_DIFFERENCE => CS::SIDE_DIFFERENCE,
        C::CARRIAGE_DIFFERENCE => CS::CARRIAGE_DIFFERENCE,
        C::MASS => CS::MASS,
        C::ACCELERATION => CS::ACCELERATION,
        C::CARGO_TYPE => CS::CARGO_TYPE,
        C::CARGO_TYPE_CODE => CS::CARGO_TYPE_CODE,
        C::DATETIME_CARGO => CS::DATETIME_CARGO,
        C::COUNT => CS::COUNT,
        C::AXIS_COUNT => CS::AXIS_COUNT,
        C::COUNTRY => CS::COUNTRY,
        C::DEPART_STATION => CS::DEPART_STATION,
        C::DEPART_STATION_CODE => CS::DEPART_STATION_CODE,
        C::PURPOSE_STATION => CS::PURPOSE_STATION,
        C::PURPOSE_STATION_CODE => CS::PURPOSE_STATION_CODE,
        C::DATETIME_SHIPMENT => CS::DATETIME_SHIPMENT,
        C::INVOICE_NUMBER => $scaleType == ScaleType::WMR ? CS::INVOICE_NUMBER_WMR : CS::INVOICE_NUMBER,
        C::INVOICE_SUPPLIER => $scaleType == ScaleType::WMR ? CS::INVOICE_SUPPLIER_WMR : CS::INVOICE_SUPPLIER,
        C::INVOICE_RECIPIENT => CS::INVOICE_RECIPIENT,
        C::INVOICE_NETTO => $scaleType == ScaleType::WMR ? CS::INVOICE_NETTO_WMR : CS::INVOICE_NETTO,
        C::INVOICE_TARE => $scaleType == ScaleType::WMR ? CS::INVOICE_TARE_WMR : CS::INVOICE_TARE,
        C::INVOICE_OVERLOAD => $scaleType == ScaleType::WMR ? CS::INVOICE_OVERLOAD_WMR : CS::INVOICE_OVERLOAD,
        C::LOADING_GROUP => CS::LOADING_GROUP,
        C::LOADING_PLACE => CS::LOADING_PLACE,
        C::TARE_NEAR_SIDE => CS::TARE_NEAR_SIDE,
        C::TARE_FAR_SIDE => CS::TARE_FAR_SIDE,
        C::TARE_FIRST_CARRIAGE => CS::TARE_FIRST_CARRIAGE,
        C::TARE_SECOND_CARRIAGE => CS::TARE_SECOND_CARRIAGE,
        C::DATETIME_FAILURE => CS::DATETIME_FAILURE,
        C::WMODE => CS::SCALE_CLASS,
        C::MESSAGE => CS::MESSAGE,
        C::UNIX_TIME_END => CS::UNIX_TIME_END,
        C::UNIT_NUMBER => CS::UNIT_NUMBER,
        C::DATETIME_T => CS::DATETIME_T,
        C::OPERATION_TYPE => CS::OPERATION_TYPE,
        C::ACCURACY_CLASS => CS::ACCURACY_CLASS,
        C::DISCRETENESS => CS::DISCRETENESS,
        C::COEFFICIENT_P1 => CS::COEFFICIENT_P1,
        C::COEFFICIENT_Q1 => CS::COEFFICIENT_Q1,
        C::COEFFICIENT_T1 => CS::COEFFICIENT_T1,
        C::COEFFICIENT_P2 => CS::COEFFICIENT_P2,
        C::COEFFICIENT_Q2 => CS::COEFFICIENT_Q2,
        C::COEFFICIENT_T2 => CS::COEFFICIENT_T2,
        C::TEMPERATURE_1 => CS::TEMPERATURE_1,
        C::TEMPERATURE_2 => CS::TEMPERATURE_2,
        C::TEMPERATURE_3 => CS::TEMPERATURE_3,
        C::TEMPERATURE_4 => CS::TEMPERATURE_4,
        C::TEMPERATURE_5 => CS::TEMPERATURE_5,
        C::TEMPERATURE_6 => CS::TEMPERATURE_6,
        C::TEMPERATURE_7 => CS::TEMPERATURE_7,
        C::TEMPERATURE_8 => CS::TEMPERATURE_8,
        C::VERIFIER => CS::VERIFIER,
        C::COMMENT => CS::COMMENT,
        C::AUTO_NUMBER => CS::AUTO_NUMBER,
        C::DRIVER => CS::DRIVER,
        C::WEIGH_NAME => CS::WEIGH_NAME,
        C::WEIGH_NAME_CODE => CS::WEIGH_NAME_CODE,
        C::PRODUCT => CS::PRODUCT,
        C::PRODUCT_CODE => CS::PRODUCT_CODE,
        C::LEFT_SIDE => CS::LEFT_SIDE,
        C::PART_CODE => CS::PART_CODE,
        C::HUMIDITY => CS::HUMIDITY,
        C::COUNT_ID => CS::COUNT_ID,
        C::COMPARE => CS::COMPARE,
        C::MI_DELTA_ABS_BRUTTO => CS::MI_DELTA_ABS_BRUTTO,
        C::MI_DELTA_ABS_BRUTTO_E => CS::MI_DELTA_ABS_BRUTTO_E,
        C::MI_DELTA_ABS_TARE => CS::MI_DELTA_ABS_TARE,
        C::MI_DELTA_ABS_TARE_E => CS::MI_DELTA_ABS_TARE_E,
        C::MI_DELTA => CS::MI_DELTA,
        C::MI_DELTA_E => CS::MI_DELTA_E,
        C::MI_TARE_DYN => CS::MI_TARE_DYN,
        C::MI_TARE_DYN_SCALES => CS::MI_TARE_DYN_SCALES,
        C::MI_TARE_DYN_DATETIME => CS::MI_TARE_DYN_BDATETIME,
        C::MI_DELTA_ABS_TARE_DYN => CS::MI_DELTA_ABS_TARE_DYN,
        C::MI_DELTA_ABS_TARE_DYN_E => CS::MI_DELTA_ABS_TARE_DYN_E,
        C::MI_DELTA_DYN => CS::MI_DELTA_DYN,
        C::MI_DELTA_DYN_E => CS::MI_DELTA_DYN_E,
        C::MI_TARE_STA => CS::MI_TARE_STA,
        C::MI_TARE_STA_SCALES => CS::MI_TARE_STA_SCALES,
        C::MI_TARE_STA_DATETIME => CS::MI_TARE_STA_BDATETIME,
        C::MI_DELTA_ABS_TARE_STA => CS::MI_DELTA_ABS_TARE_STA,
        C::MI_DELTA_ABS_TARE_STA_E => CS::MI_DELTA_ABS_TARE_STA_E,
        C::MI_DELTA_STA => CS::MI_DELTA_STA,
        C::MI_DELTA_STA_E => CS::MI_DELTA_STA_E,
        C::MI_3115_LOSS_SUPPLIER => CS::MI_3115_LOSS_SUPPLIER,
        C::MI_3115_DELTA_SUPPLIER => CS::MI_3115_DELTA_SUPPLIER,
        C::MI_3115_DELTA_FROM_TABLES => CS::MI_3115_DELTA_FROM_TABLES,
        C::MI_3115_DELTA_FOR_STATIONS => CS::MI_3115_DELTA_FOR_STATIONS,
        C::MI_3115_DELTA => CS::MI_3115_DELTA,
        C::MI_3115_TOLERANCE => CS::MI_3115_TOLERANCE,
        C::MI_3115_RESULT => CS::MI_3115_RESULT,
        C::IRON_DATE => CS::DATE,
        C::IRON_ESPC_RAZL => CS::IRON_ESPC_RAZL,
        C::IRON_ESPC => CS::IRON_ESPC,
        C::IRON_RAZL => CS::IRON_RAZL,
        C::IRON_SHCH => CS::IRON_SHCH,
        C::IRON_INGOT => CS::IRON_INGOT,
        C::IRON_CONTROL_SCALES_STA,
        C::SLAG_CONTROL_SCALES_STA => CS::CONTROL_SCALES_STA,
        C::IRON_CONTROL_SCALES_DYN,
        C::SLAG_CONTROL_SCALES_DYN => CS::CONTROL_SCALES_DYN,
        C::IRON_CONTROL_DATETIME_STA, C::SLAG_CONTROL_DATETIME_STA => CS::CONTROL_DATETIME_STA,
        C::IRON_CONTROL_DATETIME_DYN, C::SLAG_CONTROL_DATETIME_DYN => CS::CONTROL_DATETIME_DYN,
        C::IRON_CONTROL_NETTO_STA, C::SLAG_CONTROL_NETTO_STA => CS::CONTROL_NETTO_STA,
        C::IRON_CONTROL_NETTO_DYN, C::SLAG_CONTROL_NETTO_DYN => CS::CONTROL_NETTO_DYN,
        C::IRON_CONTROL_DIFF_DYN_CARR, C::SLAG_CONTROL_DIFF_DYN_CARR => CS::CONTROL_DIFF_DYN_CARR,
        C::IRON_CONTROL_DIFF_DYN_STA, C::SLAG_CONTROL_DIFF_DYN_STA => CS::CONTROL_DIFF_DYN_STA,
        C::IRON_CONTROL_DIFF_SIDE, C::SLAG_CONTROL_DIFF_SIDE => CS::CONTROL_DIFF_SIDE,
        C::IRON_CONTROL_DIFF_CARRIAGE, C::SLAG_CONTROL_DIFF_CARRIAGE => CS::CONTROL_DIFF_CARRIAGE,
        C::SENSOR_M1 => CS::SENSOR_M1,
        C::SENSOR_M2 => CS::SENSOR_M2,
        C::SENSOR_M3 => CS::SENSOR_M3,
        C::SENSOR_M4 => CS::SENSOR_M4,
        C::SENSOR_M5 => CS::SENSOR_M5,
        C::SENSOR_M6 => CS::SENSOR_M6,
        C::SENSOR_M7 => CS::SENSOR_M7,
        C::SENSOR_M8 => CS::SENSOR_M8,
        C::SENSOR_M9 => CS::SENSOR_M9,
        C::SENSOR_M10 => CS::SENSOR_M10,
        C::SENSOR_M11 => CS::SENSOR_M11,
        C::SENSOR_M12 => CS::SENSOR_M12,
        C::SENSOR_M13 => CS::SENSOR_M13,
        C::SENSOR_M14 => CS::SENSOR_M14,
        C::SENSOR_M15 => CS::SENSOR_M15,
        C::SENSOR_M16 => CS::SENSOR_M16,
        C::SENSOR_T1 => CS::SENSOR_T1,
        C::SENSOR_T2 => CS::SENSOR_T2,
        C::SENSOR_T3 => CS::SENSOR_T3,
        C::SENSOR_T4 => CS::SENSOR_T4,
        C::SENSOR_T5 => CS::SENSOR_T5,
        C::SENSOR_T6 => CS::SENSOR_T6,
        C::SENSOR_T7 => CS::SENSOR_T7,
        C::SENSOR_T8 => CS::SENSOR_T8,
        C::SENSORS_INFO_TYPE => CS::SENSORS_INFO_TYPE,
        default => $fieldName,
    };
}

function columnTitle(string $fieldName): ?string
{
    return match ($fieldName) {
        C::COMPARE => sprintf(CS::COMPARE_TITLE,
            Thresholds::COMPARE_VALUE_WARNING_YELLOW, Thresholds::COMPARE_VALUE_WARNING_RED),
        C::IRON_CONTROL_DIFF_DYN_STA => sprintf(CS::CONTROL_DIFF_DYN_STA_TITLE,
            Thresholds::IRON_CONTROL_DIFF_DYN_STA_WARNING_YELLOW, Thresholds::IRON_CONTROL_DIFF_DYN_STA_WARNING_RED),
        C::IRON_CONTROL_DIFF_SIDE => sprintf(CS::CONTROL_DIFF_SIDE_TITLE,
            Thresholds::IRON_CONTROL_DIFF_SIDE_WARNING_YELLOW, Thresholds::IRON_CONTROL_DIFF_SIDE_WARNING_RED),
        C::IRON_CONTROL_DIFF_CARRIAGE => sprintf(CS::CONTROL_DIFF_CARRIAGE_TITLE,
            Thresholds::IRON_CONTROL_DIFF_CARRIAGE_WARNING_YELLOW, Thresholds::IRON_CONTROL_DIFF_CARRIAGE_WARNING_RED),
        C::SLAG_CONTROL_DIFF_DYN_STA => sprintf(CS::CONTROL_DIFF_DYN_STA_TITLE,
            Thresholds::SLAG_CONTROL_DIFF_DYN_STA_WARNING_YELLOW, Thresholds::SLAG_CONTROL_DIFF_DYN_STA_WARNING_RED),
        C::SLAG_CONTROL_DIFF_SIDE => sprintf(CS::CONTROL_DIFF_SIDE_TITLE,
            Thresholds::SLAG_CONTROL_DIFF_SIDE_WARNING_YELLOW, Thresholds::SLAG_CONTROL_DIFF_SIDE_WARNING_RED),
        C::SLAG_CONTROL_DIFF_CARRIAGE => sprintf(CS::CONTROL_DIFF_CARRIAGE_TITLE,
            Thresholds::SLAG_CONTROL_DIFF_CARRIAGE_WARNING_YELLOW, Thresholds::SLAG_CONTROL_DIFF_CARRIAGE_WARNING_RED),
        C::AVG => sprintf(CS::AVG_TITLE,
            Thresholds::IRON_CONTROL_AVG_VALUE_WARNING_YELLOW, Thresholds::IRON_CONTROL_AVG_VALUE_WARNING_RED),
        C::SUM => sprintf(CS::SUM_TITLE,
            Thresholds::IRON_CONTROL_SUM_VALUE_WARNING_YELLOW, Thresholds::IRON_CONTROL_SUM_VALUE_WARNING_RED),
        C::IRON_ESPC => sprintf(CS::IRON_ESPC_TITLE, ScaleNums::IRON_ESPC, CargoTypes::IRON),
        C::IRON_RAZL => sprintf(CS::IRON_RAZL_TITLE, ScaleNums::IRON_RAZL, CargoTypes::IRON),
        C::IRON_SHCH => sprintf(CS::IRON_SHCH_TITLE, ScaleNums::IRON_SHCH, CargoTypes::IRON),
        C::IRON_INGOT => sprintf(CS::IRON_INGOT_TITLE, str_replace("'", "", CargoTypes::IRON_INGOT),
            SuppliersAndRecipients::IRON_SUPPLIER, SuppliersAndRecipients::IRON_RECIPIENT),
        C::IRON_CONTROL_SCALES_STA => sprintf(CS::CONTROL_SCALES_STA_TITLE, ScaleNums::IRON_COMPARE_STA, CargoTypes::IRON_COMPARE_STA),
        C::IRON_CONTROL_SCALES_DYN => sprintf(CS::CONTROL_SCALES_DYN_TITLE, ScaleNums::IRON_COMPARE_DYN, CargoTypes::IRON_COMPARE_DYN),
        C::SLAG_CONTROL_SCALES_STA => sprintf(CS::CONTROL_SCALES_STA_TITLE, ScaleNums::SLAG_COMPARE_STA, CargoTypes::SLAG_COMPARE_STA),
        C::SLAG_CONTROL_SCALES_DYN => sprintf(CS::CONTROL_SCALES_DYN_TITLE, ScaleNums::SLAG_COMPARE_DYN, CargoTypes::SLAG_COMPARE_DYN),
        C::DATETIME_SENSORS_INFO => sprintf(CS::SENSORS_INFO_TITLE,
            Thresholds::SENSORS_INFO_DATETIME_CURRENT_WARNING_YELLOW, Thresholds::SENSORS_INFO_DATETIME_CURRENT_WARNING_RED),
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
            C::SLAG_CONTROL_SCALES_STA, C::SLAG_CONTROL_SCALES_DYN,
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
            C::ID, C::COUNT, C::COUNT_ID,
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
            C::SCALE_CLASS_STATIC, C::SCALE_CLASS_DYNAMIC,
            C::IRON_ESPC_RAZL, C::IRON_ESPC, C::IRON_RAZL, C::IRON_SHCH, C::IRON_INGOT,
            C::IRON_CONTROL_NETTO_STA, C::IRON_CONTROL_NETTO_DYN,
            C::IRON_CONTROL_DIFF_DYN_CARR, C::IRON_CONTROL_DIFF_DYN_STA,
            C::IRON_CONTROL_DIFF_SIDE, C::IRON_CONTROL_DIFF_CARRIAGE,
            C::SLAG_CONTROL_NETTO_STA, C::SLAG_CONTROL_NETTO_DYN,
            C::SLAG_CONTROL_DIFF_DYN_CARR, C::SLAG_CONTROL_DIFF_DYN_STA,
            C::SLAG_CONTROL_DIFF_SIDE, C::SLAG_CONTROL_DIFF_CARRIAGE,
            C::SENSOR_M1, C::SENSOR_M2, C::SENSOR_M3, C::SENSOR_M4,
            C::SENSOR_M5, C::SENSOR_M6, C::SENSOR_M7, C::SENSOR_M8,
            C::SENSOR_M9, C::SENSOR_M10, C::SENSOR_M11, C::SENSOR_M12,
            C::SENSOR_M13, C::SENSOR_M14, C::SENSOR_M15, C::SENSOR_M16,
            C::SENSOR_T1, C::SENSOR_T2, C::SENSOR_T3, C::SENSOR_T4,
            C::SENSOR_T5, C::SENSOR_T6, C::SENSOR_T7, C::SENSOR_T8,
            C::SENSORS_INIT => false,
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

#[Pure] function mysqlDateTimeToArray($dateTime): array
{
    $result = array();
// 1981-03-29 00:00:00
    $result['year'] = substr($dateTime, 0, 4);
    $result['month'] = substr($dateTime, 5, 2);
    $result['day'] = substr($dateTime, 8, 2);
    $result['hour'] = substr($dateTime, 11, 2);
    $result['minute'] = substr($dateTime, 14, 2);
    $result['second'] = substr($dateTime, 17, 2);

    return $result;
}

#[Pure] function mysqlDateTimeToUnixTime($dateTime): int
{
    $dt = mysqlDateTimeToArray($dateTime);
    return mktime($dt['hour'], $dt['minute'], $dt['second'], $dt['month'], $dt['day'], $dt['year']);
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
    if (isset($_POST[$param])) {
        if ($_POST[$param] == "") {
            return null;
        } else {
            return $_POST[$param];
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

function getResultHeader(int $resultType): string
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
        ResultType::CARGO_LIST_DYNAMIC => S::HEADER_RESULT_CARGO_LIST_DYNAMIC,
        ResultType::CARGO_LIST_STATIC => S::HEADER_RESULT_CARGO_LIST_STATIC,
        ResultType::CARGO_LIST_AUTO => S::HEADER_RESULT_CARGO_LIST,
        ResultType::COMPARE_DYNAMIC, ResultType::COMPARE_STATIC => S::HEADER_RESULT_COMPARE,
        ResultType::COEFFS => S::HEADER_COEFFS,
        ResultType::SENSORS_ZEROS => S::SENSORS_ZEROS,
        ResultType::SENSORS_TEMPS => S::SENSORS_TEMPS,
        ResultType::SENSORS_STATUS => S::SENSORS_STATUS,
        ResultType::SENSORS_INFO => S::SENSORS_INFO,
        ResultType::IRON => S::HEADER_IRON,
        ResultType::IRON_CONTROL => S::HEADER_IRON_CONTROL,
        ResultType::SLAG_CONTROL => S::HEADER_SLAG_CONTROL,
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
 * @see \database\Info::CHARSET_LATIN
 */
#[Pure] function latin1ToUtf8(?string $s): ?string
{
    if ($s == null) return null;
    return iconv('Windows-1251', 'UTF-8', $s);
}

#[Pure] function utf8ToLatin1(?string $s): ?string
{
    if ($s == null) return null;
    return iconv('UTF-8', 'Windows-1251', $s);
}

#[Pure] function getCookieAsBool(?string $param): bool
{
    return isset($param) && isset($_COOKIE[$param]) && var_to_bool($_COOKIE[$param]);
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
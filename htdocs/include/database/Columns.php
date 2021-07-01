<?php

namespace database;

class Columns
{
    const NULL = 'NULL';

    const TRAIN_NUM = 'trnum';

    const SCALE_NUM = 'scales';

    const SCALE_TYPE_TEXT = 'type';
    const SCALE_CLASS_STATIC = 'sclass';
    const SCALE_CLASS_DYNAMIC = 'dclass';
    const SCALE_PLACE = 'place';
    const SCALE_TYPE = 'tag1';
    const SCALE_DISABLED = 'disabled';

    const SCALE_TYPE_DYN = 'typedyn';
    const SCALE_WTYPE = 'wtype';
    const SCALE_WCLASS = 'wclass';
    const SCALE_MIN_CAPACITY = 'wmin';
    const SCALE_MAX_CAPACITY = 'wmax';
    const SCALE_DISCRETENESS = 'wdiv';
    const SCALE_SENSORS_M_COUNT = 'sensors_m_count';
    const SCALE_SENSORS_T_COUNT = 'sensors_t_count';
    const SCALE_OPERATOR = 'operator';

    const SEQUENCE_NUMBER = 'num';

    const UNIX_TIME = 'wtime';
    const UNIX_TIME_END = 'etime';

    const DATETIME = 'bdatetime';
    const DATETIME_END = 'edatetime';
    const DATETIME_T = 'tdatetime';
    const DATETIME_OPERATOR = 'opdatetime';
    const DATETIME_TARE = 'idatetime_tare';
    const DATETIME_SHIPMENT = 'shipment_date';
    const DATETIME_FAILURE = 'wdatetime';
    const DATETIME_CARGO = 'cargotype_bdatetime';
    const DATETIME_SENSORS_INFO = 'sensors_info_bdatetime';

    const TRAIN_NUMBER = 'rwnum';

    const VAN_COUNT = 'nvans';
    const AXIS_COUNT = 'naxis';
    const COUNT = 'count_x';

    const CARRYING = 'carrying';
    const LOAD_NORM = 'loadnorm';
    const CARGO_TYPE = 'cargotype';
    const CARGO_TYPE_CODE = 'cargotype_code';
    const VAN_NUMBER = 'vannum';
    const VAN_TYPE = 'vantype';

    const BRUTTO = 'brutto';
    const BRUTTO_NEAR_SIDE = 'brutto_b1';
    const BRUTTO_FAR_SIDE = 'brutto_b2';
    const BRUTTO_FIRST_CARRIAGE = 'brutto_c1';
    const BRUTTO_SECOND_CARRIAGE = 'brutto_c2';
    const TARE = 'tare';
    const TARE_NEAR_SIDE = 'tare_b1';
    const TARE_FAR_SIDE = 'tare_b2';
    const TARE_FIRST_CARRIAGE = 'tare_c1';
    const TARE_SECOND_CARRIAGE = 'tare_c2';
    const TARE_MANUAL = 'tare_t';
    const TARE_DYNAMIC = 'tare_d';
    const TARE_STATIC = 'tare_s';
    const NETTO = 'netto';
    const MASS = 'mass';

    const VOLUME = 'volume';
    const OVERLOAD = 'overload';
    const TARE_TYPE = 'tareindex';
    const TARE_SCALE_NUMBER = 'iscales_tare';

    const SIDE_DIFFERENCE = 'difference_b';
    const CARRIAGE_DIFFERENCE = 'difference_c';

    const INVOICE_NETTO = 'invoice_netto';
    const INVOICE_TARE = 'invoice_tare';
    const INVOICE_OVERLOAD = 'invoice_overload';
    const INVOICE_NUMBER = 'invoice_num';
    const INVOICE_SUPPLIER = 'invoice_supplier';
    const INVOICE_RECIPIENT = 'invoice_recipient';

    const DEPART_STATION = 'depart_station';
    const PURPOSE_STATION = 'purpose_station';
    const DEPART_STATION_CODE = 'depart_station_code';
    const PURPOSE_STATION_CODE = 'purpose_station_code';
    const COUNTRY = 'country';
    const LOADING_GROUP = 'loading_group';
    const LOADING_PLACE = 'loading_place';

    const VELOCITY = 'velocity';
    const ACCELERATION = 'acceleration';

    const WMODE = 'wmode';
    const UNIT_NUMBER = 'wunit';
    const RAIL_PATH = 'railpath';
    const STATUS = 'status';
    const OPERATION_TYPE = 'optype';
    const ACCURACY_CLASS = 'pclass';
    const DISCRETENESS = 'wdiv';

    const COEFFICIENT_P1 = 'coeff1';
    const COEFFICIENT_Q1 = 'coeff2';
    const COEFFICIENT_T1 = 'coeff_temp1';
    const COEFFICIENT_P2 = 'coeff3';
    const COEFFICIENT_Q2 = 'coeff4';
    const COEFFICIENT_T2 = 'coeff_temp2';

    const TEMPERATURE_1 = 'temp1';
    const TEMPERATURE_2 = 'temp2';
    const TEMPERATURE_3 = 'temp3';
    const TEMPERATURE_4 = 'temp4';
    const TEMPERATURE_5 = 'temp5';
    const TEMPERATURE_6 = 'temp6';
    const TEMPERATURE_7 = 'temp7';
    const TEMPERATURE_8 = 'temp8';
    const VERIFIER = 'verifier';

    const OPERATOR = 'operator';
    const OPERATOR_TAB_NUMBER = 'optabn';
    const OPERATOR_SHIFT_NUMBER = 'opshift';
    const OPERATOR_SHIFT_SYMBOL = 'opsymb';

    const MESSAGE = 'message';
    const COMMENT = 'comment';

    const AUTO_NUMBER = 'auto_num';
    const DRIVER = 'driver';

    const WEIGH_NAME = 'weighname';
    const WEIGH_NAME_CODE = 'weighname_code';
    const PRODUCT = 'product';
    const PRODUCT_CODE = 'product_code';
    const LEFT_SIDE = 'leftside';
    const PART_CODE = 'part_code';
    const HUMIDITY = 'humidity';
    const COUNT_ID = 'count_id';

    const COMPARE = 'compare';

    const DATETIME_NOW = 'now()';

    const DEPARTMENT = 'department';

    const COMPANY_DATE = 'company_date';
    const DEPARTMENT_DATE = 'department_date';

    const ID = 'id';
    const NAME = 'name';
    const DEPARTMENT_NAME = 'department_name';

    const TEXT = 'text';

    const MI_DELTA_ABS_BRUTTO = 'delta_abs_brutto';
    const MI_DELTA_ABS_BRUTTO_E = 'delta_abs_brutto_exact';
    const MI_DELTA_ABS_TARE = 'delta_abs_tare';
    const MI_DELTA_ABS_TARE_E = 'delta_abs_tare_exact';
    const MI_DELTA = 'delta';
    const MI_DELTA_E = 'delta_exact';
    const MI_TARE_DYN = 'tare_dyn';
    const MI_TARE_DYN_SCALES = 'tare_dyn_scales';
    const MI_TARE_DYN_DATETIME = 'tare_dyn_bdatetime';
    const MI_DELTA_ABS_TARE_DYN = 'delta_abs_tare_dyn';
    const MI_DELTA_ABS_TARE_DYN_E = 'delta_abs_tare_dyn_exact';
    const MI_DELTA_DYN = 'delta_dyn';
    const MI_DELTA_DYN_E = 'delta_dyn_exact';
    const MI_TARE_STA = 'tare_sta';
    const MI_TARE_STA_SCALES = 'tare_sta_scales';
    const MI_TARE_STA_DATETIME = 'tare_sta_bdatetime';
    const MI_DELTA_ABS_TARE_STA = 'delta_abs_tare_sta';
    const MI_DELTA_ABS_TARE_STA_E = 'delta_abs_tare_sta_exact';
    const MI_DELTA_STA = 'delta_sta';
    const MI_DELTA_STA_E = 'delta_sta_exact';

    const MI_3115_LOSS_SUPPLIER = 'loss_supplier';
    const MI_3115_DELTA_SUPPLIER = 'delta_supplier';
    const MI_3115_DELTA_FROM_TABLES = 'delta_from_tables';
    const MI_3115_DELTA_FOR_STATIONS = 'delta_for_stations';
    const MI_3115_DELTA = 'delta_mi_3115';
    const MI_3115_TOLERANCE = 'tolerance_mi_3115';
    const MI_3115_RESULT = 'result_mi_3115';

    const IRON_DATE = 'date';
    const IRON_ESPC_RAZL = 'espc_razl';
    const IRON_ESPC = 'espc';
    const IRON_RAZL = 'razl';
    const IRON_SHCH = 'shch';
    const IRON_INGOT = 'ingot';

    const IRON_CONTROL_SCALES_STA = 'iron_control_scales_sta';
    const IRON_CONTROL_DATETIME_STA = 'iron_control_bdatetime_sta';
    const IRON_CONTROL_SCALES_DYN = 'iron_control_scales_dyn';
    const IRON_CONTROL_DATETIME_DYN = 'iron_control_bdatetime_dyn';
    const IRON_CONTROL_NETTO_STA = 'iron_control_netto_sta';
    const IRON_CONTROL_NETTO_DYN = 'iron_control_netto_dyn';
    const IRON_CONTROL_DIFF_DYN_CARR = 'iron_control_diff_dyn_carr';
    const IRON_CONTROL_DIFF_DYN_STA = 'iron_control_diff_dyn_sta';
    const IRON_CONTROL_DIFF_SIDE = 'iron_control_diff_side';
    const IRON_CONTROL_DIFF_CARRIAGE = 'iron_control_diff_carriage';

    const SLAG_CONTROL_SCALES_STA = 'slag_control_scales_sta';
    const SLAG_CONTROL_DATETIME_STA = 'slag_control_bdatetime_sta';
    const SLAG_CONTROL_SCALES_DYN = 'slag_control_scales_dyn';
    const SLAG_CONTROL_DATETIME_DYN = 'slag_control_bdatetime_dyn';
    const SLAG_CONTROL_NETTO_STA = 'slag_control_netto_sta';
    const SLAG_CONTROL_NETTO_DYN = 'slag_control_netto_dyn';
    const SLAG_CONTROL_DIFF_DYN_CARR = 'slag_control_diff_dyn_carr';
    const SLAG_CONTROL_DIFF_DYN_STA = 'slag_control_diff_dyn_sta';
    const SLAG_CONTROL_DIFF_SIDE = 'slag_control_diff_side';
    const SLAG_CONTROL_DIFF_CARRIAGE = 'slag_control_diff_carriage';

    const AVG = 'avg_x';
    const SUM = 'sum_x';

    const SENSOR_M = 'm';
    const SENSOR_M1 = 'm1';
    const SENSOR_M2 = 'm2';
    const SENSOR_M3 = 'm3';
    const SENSOR_M4 = 'm4';
    const SENSOR_M5 = 'm5';
    const SENSOR_M6 = 'm6';
    const SENSOR_M7 = 'm7';
    const SENSOR_M8 = 'm8';
    const SENSOR_M9 = 'm9';
    const SENSOR_M10 = 'm10';
    const SENSOR_M11 = 'm11';
    const SENSOR_M12 = 'm12';
    const SENSOR_M13 = 'm13';
    const SENSOR_M14 = 'm14';
    const SENSOR_M15 = 'm15';
    const SENSOR_M16 = 'm16';
    const SENSORS_INIT = 'init';
    const SENSOR_T = 't';
    const SENSOR_T1 = 't1';
    const SENSOR_T2 = 't2';
    const SENSOR_T3 = 't3';
    const SENSOR_T4 = 't4';
    const SENSOR_T5 = 't5';
    const SENSOR_T6 = 't6';
    const SENSOR_T7 = 't7';
    const SENSOR_T8 = 't8';
    const SENSORS_INFO_TYPE = 'type_i';
}
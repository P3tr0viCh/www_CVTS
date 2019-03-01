<?php

namespace Database;

class Info
{
    const WDB = 'wdb3';
    const CVTS = 'cvts';
    const CHARSET = 'latin1';
}

class Tables
{
    const VAN_DYNAMIC_BRUTTO = 'vndynb';
    const VAN_DYNAMIC_TARE = 'vndynt';
    const VAN_STATIC_BRUTTO = 'vnstab';
    const VAN_STATIC_TARE = 'vnstat';

    const VAN_DYNAMIC_AND_STATIC_BRUTTO = 'vndynb_temp';
    const VAN_BRUTTO_ADD = 'vnb_add';

    const VAN_DELTAS = 'vnb_delta';

    const TRAIN_DYNAMIC = 'trdynb';

    const AUTO_BRUTTO = 'autob';
    const AUTO_TARE = 'autot';

    const KANAT = 'kanatb';

    const DP = 'dpb';

    const SCALES = 'scalesinfo';
    const SCALES_ADD = 'scalesinfo_add';

    const COEFFS = 'wcalver';

    const ACCIDENTS = 'accidents';
    const DEPARTMENTS = 'departments';
}

class Columns
{
    const ALL = '*';

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
    const SCALE_CLASS = 'wclass';
    const SCALE_MIN_CAPACITY = 'wmin';
    const SCALE_MAX_CAPACITY = 'wmax';
    const SCALE_DISCRETENESS = 'wdiv';

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

    const TRAIN_NUMBER = 'rwnum';

    const VAN_COUNT = 'nvans';
    const AXIS_COUNT = 'naxis';

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
    const PRODUCT = 'product';
    const LEFT_SIDE = 'leftside';
    const COUNT_ID = 'count_id';

    const COMPARE = 'compare';

    const DATETIME_NOW = 'now()';

    const DEPARTMENT = 'department';

    const COMPANY_DATE = 'company_date';
    const DEPARTMENT_DATE = 'department_date';

    const ID = 'id';
    const NAME = 'name';
    const DEPARTMENT_NAME = 'department_name';

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

    const IRON_DATE = 'date';
    const IRON_ESPC_RAZL = 'espc_razl';
    const IRON_ESPC = 'espc';
    const IRON_RAZL = 'razl';
    const IRON_SHCH = 'shch';
}
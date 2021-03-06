<?php

namespace database;

class Tables
{
    const VAN_DYNAMIC_BRUTTO = 'vndynb';
    const VAN_DYNAMIC_TARE = 'vndynt';
    const VAN_STATIC_BRUTTO = 'vnstab';
    const VAN_STATIC_TARE = 'vnstat';

    const VAN_BRUTTO_ADD = 'vnb_add';

    const VAN_DELTAS = 'vnb_delta';
    const VAN_DELTAS_MI_3115 = 'vnb_delta_mi_3115';

    const TRAIN_DYNAMIC = 'trdynb';

    const AUTO_BRUTTO = 'autob';
    const AUTO_TARE = 'autot';

    const KANAT = 'kanatb';

    const DP = 'dpb';

    const SCALES = 'scalesinfo';
    const SCALES_ADD = 'scalesinfo_add';

    const LST_WCLASS = 'lst_wclass';
    const LST_OPERATOR = 'lst_operator';

    const COEFFS = 'wcalver';

    const SENSORS_ZEROS = 'wd30log_z';
    const SENSORS_TEMPS = 'wd30log_t';
    const SENSORS_STATUS = 'wd30log_s';

    const ACCIDENTS = 'accidents';
    const DEPARTMENTS = 'departments';
}
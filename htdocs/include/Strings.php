<?php

class Strings
{
    const TAB = "  ";

    const EXCEL_SEPARATOR = ";";
    const EXCEL_EOL = "\n";

    const MAIN_TITLE = '������ ������� ������';
    const DATETIME_TITLE = '���� � �����';
    const WAC_TITLE = '����������';
    const MYSQL_DATETIME_TITLE = '���� � ����� �������';
    const ERROR_INCOMPATIBLE_BROWSER_TITLE = '������������� �������';

    const MAIN_HEADER = '��� ����������������� ��������������� ������';
    const MAIN_HEADER_LOGO_ALT = '������������� | ��������� �����';
    const MYSQL_DATETIME_HEADER = '���� � ����� ������� MySQL';

    const ERROR_JS_DISABLED = 'JavaScript ��������';

    const ERROR_404 = '404';
    const ERROR_404_TITLE = '404';
    const ERROR_404_HEADER = '����� �������� ���';
    const ERROR_404_SUB_HEADER = '�� �� ���������. ����� ��� �������, �������� ���������� � ��������';

    const ERROR_403 = '403';
    const ERROR_403_TITLE = '403';
    const ERROR_403_HEADER = '������ ��������';
    const ERROR_403_SUB_HEADER = '�� �� ���������. ����� ��� �������, �������� ���������� � ��������';

    const ERROR_INCOMPATIBLE_BROWSER_HEADER = '������������� �������';
    const ERROR_INCOMPATIBLE_BROWSER_SUB_HEADER = '�� �� ���������. ����� ��� �������, �������� ���������� � ��������';

    const ERROR_GOTO_START = '������� �� ��������� ��������';
    const ERROR_GOTO_START_COMPATIBLE = '������� �� ��������� �������� � ������ �������';

    const DRAWER_TITLE = '����';
    const DRAWER_START_PAGE = '����� ������';
    const DRAWER_ALL_TRAIN_SCALES = '��� ��������������� ����';

    const FOOTER_LEFT_SECTION = '�������������, ��������� �����';
    const FOOTER_RIGHT_SECTION = '� ������ ���������� ��������, ����, 2004-2016';

    const ALL_TRAIN_SCALES = '������� ���������� �� ���� ��������������� �����';

    const SCALE_INFO_ALL_TRAIN_PLACE = '�� ����';
    const SCALE_INFO_ALL_TRAIN_HEADER = '������� ���������� �� ���� ��������������� �����';
    const SCALE_INFO_HEADER = '%s, ���� �%d';

    const HEADER_INFO = '����������';
    const HEADER_DYNAMIC = '������������ �����������';
    const HEADER_STATIC = '����������� �����������';
    const HEADER_RESULTS = '���������� �����������';
    const HEADER_PERIOD = '������';
    const HEADER_PERIOD_START = '��������� ���� � �����';
    const HEADER_PERIOD_END = '�������� ���� � �����';
    const HEADER_SEARCH = '�����';
    const HEADER_SETTINGS = '���������';
    const HEADER_COMPARE = '��������� �����';

    const HEADER_LOADING = '��������. ���������...';

    const HEADER_RESULT_VN_DYN_B = '������������ �����������, ������ (������)';
    const HEADER_RESULT_VN_DYN_T = '������������ �����������, ������ (����)';
    const HEADER_RESULT_VN_STA_B = '����������� �����������, ������ (������)';
    const HEADER_RESULT_VN_STA_T = '����������� �����������, ������ (����)';
    const HEADER_RESULT_TR_DYN = '������������ �����������, �������';
    const HEADER_RESULT_TR_DYN_ONE = '�������� �������';
    const HEADER_RESULT_AUTO_B = '����������� ����������� ������';
    const HEADER_RESULT_AUTO_T = '����������� ����������� ����';
    const HEADER_RESULT_KANAT = '������ ��������';
    const HEADER_RESULT_DP = '������ ��������';
    const HEADER_RESULT_DP_SUM = '����� ��������';
    const HEADER_RESULT_CARGO_LIST = '������ ������';
    const HEADER_RESULT_COMPARE = '����������� �������, ������';

    const HEADER_RESULT_PERIOD_DATE = '%s %s';
    const HEADER_RESULT_PERIOD_FROM = '%s � %s';
    const HEADER_RESULT_PERIOD_TO = '%s � ������ ������ �� %s';
    const HEADER_RESULT_PERIOD_FROM_TO = '%s � %s �� %s';
    const HEADER_RESULT_PERIOD_ALL = '%s �� �� ����� ������';

    const HEADER_RESULT_SEARCH = '�����:';
    const HEADER_RESULT_SEARCH_AUTO_NUMBER = '����� ����������';
    const HEADER_RESULT_SEARCH_VAN_NUMBER = '����� ������';
    const HEADER_RESULT_SEARCH_CARGO_TYPE = '��� �����';
    const HEADER_RESULT_SEARCH_INVOICE_NUM = '����� ���������';
    const HEADER_RESULT_SEARCH_INVOICE_SUPPLIER = '����������������';
    const HEADER_RESULT_SEARCH_INVOICE_RECIPIENT = '���������������';
    const HEADER_RESULT_SEARCH_SCALES = '������ �����';

    const HEADER_RESULT_SEARCH_COMPARE = '��������� �������� %s ����� �������� (����� %s)';
    const HEADER_RESULT_SEARCH_COMPARE_BY_BRUTTO = '������';
    const HEADER_RESULT_SEARCH_COMPARE_BY_NETTO = '�����';
    const HEADER_RESULT_SEARCH_COMPARE_FORWARD = '������';
    const HEADER_RESULT_SEARCH_COMPARE_BACKWARD = '������';

    const HEADER_WAC = '���������� ����, ������������ ��� �����';
    const HEADER_WAC_DEPARTMENT = '����';
    const HEADER_WAC_COMPANY = '��������';

    const TEXT_ZERO_RESULT = '�� �������� ���������� ������� ������ ���';

    const TEXT_COMPATIBLE_BROWSERS_START = '����� ������ �������������� ���������� ����������:';
    const TEXT_COMPATIBLE_BROWSER_IE = 'Microsoft Internet Explorer %d';
    const TEXT_COMPATIBLE_BROWSER_EDGE = 'Microsoft Edge %d';
    const TEXT_COMPATIBLE_BROWSER_CHROME = 'Google Chrome %d (������� �������� �� ������ Google Chromium, ��������, ������.�������)';
    const TEXT_COMPATIBLE_BROWSERS_END = '������ �������� ������ ���� ����� ��� ���� ���� ���������. JavaScript ������ ���� ��������.';

    const BUTTON_TRAINS = '������, ������';
    const BUTTON_VANS_BRUTTO = '������, ������';
    const BUTTON_VANS_TARE = '������, ����';
    const BUTTON_CARGOS = '������ ������';
    const BUTTON_COMPARE = '��������� �����';
    const BUTTON_BRUTTO = '������';
    const BUTTON_TARE = '����';
    const BUTTON_VIEW = '��������';
    const BUTTON_SUM_FOR_PERIOD = '����� �� ������';
    const BUTTON_CLEAR = '��������';

    const CHECKBOX_ORDER_BY_DATETIME = '���������� ������ �� ���� � �������';
    const CHECKBOX_ALL_FIELDS = '��������� �������';
    const CHECKBOX_SHOW_CARGO_DATE = '�������� ����� ��������� ���� �����';
    const CHECKBOX_COMPARE_FORWARD = '����� ������';
    const CHECKBOX_COMPARE_BY_BRUTTO = '���������� ������';
    const CHECKBOX_ONLY_CHARK = '������ ����';

    const MENU_DATES_CURRENT_DAY = '������ �������� ���';
    const MENU_DATES_CURRENT_MONTH = '������ �������� ������';
    const MENU_DATES_CURRENT_WEEK = '������ ������� ������';
    const MENU_DATES_PREV_DAY = '������� �����';
    const MENU_DATES_FROM_5_TO_5 = '� 05:00 �������� ��� �� 04:59 ��������';
    const MENU_DATES_FROM_20_TO_20 = '� 20:00 �������� ��� �� 19:59 ��������';
    const MENU_DATES_CLEAR = '��������';

    const MENU_COPY_ALL = '���������� ��';
    const MENU_COPY_TABLE = '���������� ������� � �����������';
    const MENU_COPY_TABLE_BODY = '���������� ������ �� �������';

    const NAV_LINK_CLEAR = '��������';
    const NAV_LINK_BACK = '�����';
    const NAV_LINK_UPDATE = '��������';
    const NAV_LINK_SAVE = '��������� � EXCEL';
    const NAV_LINK_SAVE_OLD = '��������� � Excel';

    const INPUT_DAY = '����';
    const INPUT_DAY_HELP = '����� �� 1 �� 31';
    const INPUT_DAY_PATTERN = '0[1-9]|1[0-9]|2[0-9]|3[01]|[1-9]';
    const INPUT_MONTH = '�����';
    const INPUT_MONTH_HELP = '����� �� 1 �� 12';
    const INPUT_MONTH_PATTERN = '0[1-9]|1[012]|[1-9]';
    const INPUT_YEAR = '���';
    const INPUT_YEAR_HELP = '������ �����';
    const INPUT_YEAR_PATTERN = '[0-9]{4}';
    const INPUT_HOUR = '���';
    const INPUT_HOUR_HELP = '����� �� 0 �� 23';
    const INPUT_HOUR_PATTERN = '0[0-9]|1[0-9]|2[0-3]|[0-9]';
    const INPUT_MINUTES = '������';
    const INPUT_MINUTES_HELP = '����� �� 0 �� 59';
    const INPUT_MINUTES_PATTERN = '0[0-9]|[0-5][0-9]|[0-9]';
    const INPUT_VAN_NUMBER = '����� ������';
    const INPUT_VAN_NUMBER_HELP = '������ ����� � ������� % � _, �������� 8 ������';
    const INPUT_VAN_NUMBER_PATTERN = '[0-9%_]{0,8}';
    const INPUT_AUTO_NUMBER = '����� ����������';
    const INPUT_AUTO_NUMBER_HELP = '���������-�������� ������� � ������� % � _, �������� 9 ������';
    const INPUT_AUTO_NUMBER_PATTERN = '[�-��-߸�a-zA-Z0-9%_]{0,9}';
    const INPUT_CARGO_TYPE = '��� �����';
    const INPUT_INVOICE_NUM = '����� ���������';
    const INPUT_INVOICE_SUPPLIER = '����������������';
    const INPUT_INVOICE_RECIPIENT = '���������������';
    const INPUT_SCALES = '������ �����';
    const INPUT_SCALES_HELP = '������ ����� � �������';
    const INPUT_SCALES_PATTERN = '^[0-9,]+$';

    const HELP_PERIOD_OLD = '(����.�����.��� ���:������)<br>(���� ��� ����� ��������� ������� �� �����������)<br>(���� ���-�� �� �������, ��������������� ������� ����)';
    const HELP_SEARCH = '������ �%� (������� ������������ ��� ������ ������ ��������, �_� (������������� � ��� ������ ������ �������';
    const HELP_SEARCH_OLD = '(����������� ������ "%" ��� ������ ������ ��������, "_" ��� ������ ������ �������)';
    const HELP_SCALES = '������ ����� �������� ����� �������';
    const HELP_SCALES_OLD = '(������ ����� �������� ����� �������)';

    const TEXT_SCALE_CLASS_STATIC = '�������';
    const TEXT_SCALE_CLASS_DYNAMIC = '��������';
    const TEXT_SCALE_CLASS_UNKNOWN = '�� ����������';

    const TEXT_OPERATION_TYPE_CALIBRATION_DYNAMIC = '���������� � ��������';
    const TEXT_OPERATION_TYPE_CALIBRATION_STATIC = '���������� � �������';
    const TEXT_OPERATION_TYPE_VERIFICATION_DYNAMIC = '������� � ��������';
    const TEXT_OPERATION_TYPE_VERIFICATION_STATIC = '������� � �������';
    const TEXT_OPERATION_TYPE_MAINTENANCE = '���. ������������';
    const TEXT_OPERATION_TYPE_REPAIR = '������';
    const TEXT_OPERATION_TYPE_UNKNOWN = '�� ����������';

    const TEXT_TARE_TYPE_MANUAL = '��������';
    const TEXT_TARE_TYPE_DYNAMIC = '��������';
    const TEXT_TARE_TYPE_STATIC = '�������';
    const TEXT_TARE_TYPE_UNKNOWN = '�� ����������';

    const TEXT_SIDE_RIGHT = '������';
    const TEXT_SIDE_LEFT = '�����';
    const TEXT_SIDE_UNKNOWN = '�� ����������';

    const TEXT_NIGHT_MODE = '����/����';

    const ERROR_ERROR = '������';
    const ERROR_MYSQL_CONNECTION = '��� ����������� � ������� MySQL';
    const ERROR_MYSQL_QUERY = '�� ������� ��������� ������ � ���� ������';
    const ERROR_MYSQL_DETAILS = '������ %d: %s';
    const ERROR_MYSQL_CONNECTION_FILE_ERROR = '���� MYSQL_CONNECTION �� ������ ��� ����� ������������ ������';
    const ERROR_MYSQL_ZERO = '�������� ������ � ���� ������';
    const ERROR_MYSQL_BAD_SCALE_NUM = '���� � ������� %d � ���� ������ �� �������';
    const ERROR_MYSQL_MAX_LIMIT = '��������� ������� �������� ���������� �������<br>���������� ������� ������� ������ �������<br>�/��� ������� ��������� ������.';
}
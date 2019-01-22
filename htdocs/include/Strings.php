<?php

class Strings
{
    const TAB = "  ";

    const DEC_POINT = ",";

    // Byte Order Mark
    const EXCEL_BOM = "\xEF\xBB\xBF";
    const EXCEL_SEPARATOR = ";";
    const EXCEL_EOL = "\n";

    const TITLE_MAIN = 'Сервер весовых систем';
    const TITLE_MAIN_BACKUP = 'Резервный сервер весовых систем';
    const TITLE_DATETIME = 'Дата и время';
    const TITLE_TEMP = 'Температура';
    const TITLE_WAC = 'Травматизм';
    const TITLE_MYSQL_DATETIME = 'Дата и время сервера';
    const TITLE_ERROR_INCOMPATIBLE_BROWSER = 'Несовместимый браузер';
    const TITLE_HELP = 'Справка';
    const TITLE_A = 'Карта сайта';
    const TITLE_ERROR = 'Ошибка';

    const HEADER_PAGE_MAIN = 'ЦЕХ ВЕСОИЗМЕРИТЕЛЬНЫХ ТЕХНОЛОГИЧЕСКИХ СИСТЕМ';
    const HEADER_PAGE_MAIN_BACKUP = 'РЕЗЕРВНЫЙ СЕРВЕР';
    const HEADER_PAGE_MAIN_LOGO_ALT = 'Металлоинвест | Уральская сталь';
    const HEADER_PAGE_MYSQL_DATETIME = 'Дата и время на сервере MySQL';
    const HEADER_PAGE_HELP = 'Справка';
    const HEADER_PAGE_A = 'Карта сайта';

    const DRAWER_TITLE = 'Весы';
    const DRAWER_START_PAGE = 'Общий список';
    const DRAWER_ALL_TRAIN_SCALES = 'Все железнодорожные весы';

    const FOOTER_LEFT_SECTION = 'МЕТАЛЛОИНВЕСТ, Уральская Сталь';
    const FOOTER_RIGHT_SECTION = '© Дураев Константин Петрович';

    const ALL_TRAIN_SCALES = 'Сводная информация по всем железнодорожным весам';

    const SCALE_INFO_ALL_TRAIN_PLACE = 'ЖД весы';
    const SCALE_INFO_ALL_TRAIN_HEADER = 'Сводная информация по всем железнодорожным весам';
    const SCALE_INFO_HEADER = '%s, весы №%d';

    const HEADER_INFO = 'Информация';
    const HEADER_DYNAMIC = 'Динамическое взвешивание';
    const HEADER_STATIC = 'Статическое взвешивание';
    const HEADER_RESULTS = 'Результаты взвешиваний';
    const HEADER_SERVICE = 'Сервис';
    const HEADER_PERIOD = 'Период';
    const HEADER_PERIOD_START = 'Начальная дата и время';
    const HEADER_PERIOD_END = 'Конечная дата и время';
    const HEADER_SEARCH = 'Поиск';
    const HEADER_SETTINGS = 'Настройки';
    const HEADER_COMPARE = 'Сравнение массы';

    const HEADER_LOADING = 'Загрузка. Подождите...';

    const HEADER_RESULT_VN_DYN_B = 'Динамическое взвешивание брутто, вагоны';
    const HEADER_RESULT_VN_DYN_T = 'Динамическое взвешивание тары, вагоны';
    const HEADER_RESULT_VN_STA_B = 'Статическое взвешивание брутто, вагоны';
    const HEADER_RESULT_VN_STA_T = 'Статическое взвешивание тары, вагоны';
    const HEADER_RESULT_TR_DYN = 'Динамическое взвешивание, составы';
    const HEADER_RESULT_TR_DYN_ONE = 'Провеска состава';
    const HEADER_RESULT_AUTO_B = 'Статическое взвешивание брутто';
    const HEADER_RESULT_AUTO_T = 'Статическое взвешивание тары';
    const HEADER_RESULT_KANAT = 'Список провесок';
    const HEADER_RESULT_DP = 'Список провесок';
    const HEADER_RESULT_DP_SUM = 'Сумма провесок';
    const HEADER_RESULT_CARGO_LIST = 'Список грузов';
    const HEADER_RESULT_COMPARE = 'Взвешивание вагонов, брутто';
    const HEADER_COEFF = 'Коэффициенты';

    const HEADER_RESULT_PERIOD_DATE = '%s %s';
    const HEADER_RESULT_PERIOD_FROM = '%s с %s';
    const HEADER_RESULT_PERIOD_TO = '%s с начала работы по %s';
    const HEADER_RESULT_PERIOD_FROM_TO = '%s с %s по %s';
    const HEADER_RESULT_PERIOD_ALL = '%s за всё время работы';

    const HEADER_RESULT_SEARCH = 'Поиск:';
    const HEADER_RESULT_SEARCH_AUTO_NUMBER = 'Номер автомобиля';
    const HEADER_RESULT_SEARCH_VAN_NUMBER = 'Номер вагона';
    const HEADER_RESULT_SEARCH_CARGO_TYPE = 'Род груза';
    const HEADER_RESULT_SEARCH_INVOICE_NUM = 'Номер накладной';
    const HEADER_RESULT_SEARCH_INVOICE_SUPPLIER = 'Грузоотправитель';
    const HEADER_RESULT_SEARCH_INVOICE_RECIPIENT = 'Грузополучатель';
    const HEADER_RESULT_SEARCH_SCALES = 'Номера весов';

    const HEADER_RESULT_SEARCH_COMPARE = 'Сравнение значений %s между весовыми (поиск %s)';
    const HEADER_RESULT_SEARCH_COMPARE_BY_BRUTTO = 'брутто';
    const HEADER_RESULT_SEARCH_COMPARE_BY_NETTO = 'нетто';
    const HEADER_RESULT_SEARCH_COMPARE_FORWARD = '«вперёд»';
    const HEADER_RESULT_SEARCH_COMPARE_BACKWARD = '«назад»';

    const HEADER_WAC = 'Количество дней, отработанных без травм';
    const HEADER_WAC_DEPARTMENT = 'Цех №%s';
    const HEADER_WAC_COMPANY = 'Общество';

    const TEXT_ZERO_RESULT = 'По заданным параметрам запроса данных нет';

    // IE6 fix.
    const TEXT_TABLE_CELL_EMPTY = '<span style=\'zoom: 1;\'></span>';

    const TEXT_COMPATIBLE_BROWSERS_START = 'Новый дизайн поддерживается следующими браузерами:';
    const TEXT_COMPATIBLE_BROWSER_IE = 'Microsoft Internet Explorer %d';
    const TEXT_COMPATIBLE_BROWSER_EDGE = 'Microsoft Edge %d';
    const TEXT_COMPATIBLE_BROWSER_CHROME = 'Google Chrome %d (включая браузеры на основе Google Chromium, например, Яндекс.Браузер)';
    const TEXT_COMPATIBLE_BROWSERS_END = 'Версия браузера должна быть равна или быть выше указанной. JavaScript должен быть разрешён.';

    const BUTTON_TRAINS = 'Поезда, брутто';
    const BUTTON_VANS_BRUTTO = 'Вагоны, брутто';
    const BUTTON_VANS_TARE = 'Вагоны, тара';
    const BUTTON_CARGOS = 'Список грузов';
    const BUTTON_COMPARE = 'Сравнение массы';
    const BUTTON_BRUTTO = 'Брутто';
    const BUTTON_TARE = 'Тара';
    const BUTTON_VIEW = 'Просмотр';
    const BUTTON_SUM_FOR_PERIOD = 'Сумма за период';
    const BUTTON_COEFFS = 'Коэффициенты';
    const BUTTON_CLEAR = 'Очистить';

    const CHECKBOX_ORDER_BY_DATETIME = 'Сортировка только по дате и времени';
    const CHECKBOX_ALL_FIELDS = 'Подробные таблицы';
    const CHECKBOX_SHOW_CARGO_DATE = 'Показать время изменения рода груза';
    const CHECKBOX_SHOW_DELTAS = 'Показать предельно допускаемые погрешности';
    const CHECKBOX_COMPARE_FORWARD = 'Поиск «вперёд»';
    const CHECKBOX_COMPARE_BY_BRUTTO = 'Сравнивать брутто';
    const CHECKBOX_ONLY_CHARK = 'Только кокс';

    const MENU_DATES_CURRENT_DAY = 'Начало текущего дня';
    const MENU_DATES_CURRENT_MONTH = 'Начало текущего месяца';
    const MENU_DATES_CURRENT_WEEK = 'Начало текущей недели';
    const MENU_DATES_PREV_DAY = 'Прошлые сутки';
    const MENU_DATES_FROM_5_TO_5 = 'С 05:00 прошлого дня до 04:59 текущего';
    const MENU_DATES_FROM_20_TO_20 = 'С 20:00 прошлого дня до 19:59 текущего';
    const MENU_DATES_CLEAR = 'Очистить';

    const MENU_COPY_ALL = 'Копировать всё';
    const MENU_COPY_TABLE = 'Копировать таблицу с заголовками';
    const MENU_COPY_TABLE_BODY = 'Копировать данные из таблицы';

    const NAV_LINK_CLEAR = 'ОЧИСТИТЬ';
    const NAV_LINK_BACK = 'НАЗАД';
    const NAV_LINK_UPDATE = 'ОБНОВИТЬ';
    const NAV_LINK_SAVE = 'СОХРАНИТЬ В EXCEL';
    const NAV_LINK_SAVE_OLD = 'Сохранить в Excel';

    const INPUT_DAY = 'День';
    const INPUT_DAY_HELP = 'Число от 1 до 31';
    const INPUT_DAY_PATTERN = '0[1-9]|1[0-9]|2[0-9]|3[01]|[1-9]';
    const INPUT_MONTH = 'Месяц';
    const INPUT_MONTH_HELP = 'Число от 1 до 12';
    const INPUT_MONTH_PATTERN = '0[1-9]|1[012]|[1-9]';
    const INPUT_YEAR = 'Год';
    const INPUT_YEAR_HELP = 'Четыре цифры';
    const INPUT_YEAR_PATTERN = '[0-9]{4}';
    const INPUT_HOUR = 'Час';
    const INPUT_HOUR_HELP = 'Число от 0 до 23';
    const INPUT_HOUR_PATTERN = '0[0-9]|1[0-9]|2[0-3]|[0-9]';
    const INPUT_MINUTES = 'Минуты';
    const INPUT_MINUTES_HELP = 'Число от 0 до 59';
    const INPUT_MINUTES_PATTERN = '0[0-9]|[0-5][0-9]|[0-9]';
    const INPUT_VAN_NUMBER = 'Номер вагона';
    const INPUT_VAN_NUMBER_HELP = 'Только цифры и символы % и _, максимум 8 знаков';
    const INPUT_VAN_NUMBER_PATTERN = '[0-9%_]{0,8}';
    const INPUT_AUTO_NUMBER = 'Номер автомобиля';
    const INPUT_AUTO_NUMBER_HELP = 'Алфавитно-цифровые символы и символы % и _, максимум 9 знаков';
    const INPUT_AUTO_NUMBER_PATTERN = '[а-яА-ЯёЁa-zA-Z0-9%_]{0,9}';
    const INPUT_CARGO_TYPE = 'Род груза';
    const INPUT_INVOICE_NUM = 'Номер накладной';
    const INPUT_INVOICE_SUPPLIER = 'Грузоотправитель';
    const INPUT_INVOICE_RECIPIENT = 'Грузополучатель';
    const INPUT_SCALES = 'Номера весов';
    const INPUT_SCALES_HELP = 'Только цифры и запятые';
    const INPUT_SCALES_PATTERN = '^[0-9,]+$';

    const HELP_PERIOD_OLD = '(день.месяц.год час:минуты)<br>(дату или время полностью вводить не обязательно)<br>(если что-то не указано, подразумевается текущая дата)';
    const HELP_SEARCH = 'Символ «%» (процент) используется для замены группы символов, «_» (подчёркивание) – для замены одного символа';
    const HELP_SEARCH_OLD = '(используйте символ "%" для замены группы символов, "_" для замены одного символа)';
    const HELP_SCALES = 'Номера весов вводятся через запятую';
    const HELP_SCALES_OLD = '(номера весов вводятся через запятую)';

    const TEXT_SCALE_CLASS_STATIC = 'Статика';
    const TEXT_SCALE_CLASS_STATIC_0 = 'Средний';
    const TEXT_SCALE_CLASS_STATIC_1 = 'Обычный';
    const TEXT_SCALE_CLASS_STATIC_2 = 'Высокий';
    const TEXT_SCALE_CLASS_DYNAMIC = 'Динамика';
    const TEXT_SCALE_CLASS_DYNAMIC_0 = '0.2';
    const TEXT_SCALE_CLASS_DYNAMIC_1 = '0.5';
    const TEXT_SCALE_CLASS_DYNAMIC_2 = '1';
    const TEXT_SCALE_CLASS_DYNAMIC_3 = '2';
    const TEXT_SCALE_CLASS_DYNAMIC_4 = '5';
    const TEXT_SCALE_CLASS_UNKNOWN = 'Не определено';

    const TEXT_OPERATION_TYPE_CALIBRATION_DYNAMIC = 'Калибровка в динамике';
    const TEXT_OPERATION_TYPE_CALIBRATION_STATIC = 'Калибровка в статике';
    const TEXT_OPERATION_TYPE_VERIFICATION_DYNAMIC = 'Поверка в динамике';
    const TEXT_OPERATION_TYPE_VERIFICATION_STATIC = 'Поверка в статике';
    const TEXT_OPERATION_TYPE_MAINTENANCE = 'Тех. обслуживание';
    const TEXT_OPERATION_TYPE_REPAIR = 'Ремонт';
    const TEXT_OPERATION_TYPE_UNKNOWN = 'Не определено';

    const TEXT_TARE_TYPE_MANUAL = 'Трафарет';
    const TEXT_TARE_TYPE_DYNAMIC = 'Динамика';
    const TEXT_TARE_TYPE_STATIC = 'Статика';
    const TEXT_TARE_TYPE_UNKNOWN = 'Не определено';

    const TEXT_SIDE_RIGHT = 'Правая';
    const TEXT_SIDE_LEFT = 'Левая';
    const TEXT_SIDE_UNKNOWN = 'Не определено';

    const TEXT_NIGHT_MODE = 'ДЕНЬ/НОЧЬ';

    const TEXT_APACHE_VERSION = 'Версия Apache: %s';
    const TEXT_PHP_VERSION = 'Версия PHP: %s';
    const TEXT_SITE_VERSION = 'Версия сайта: %s от %s';

    const ERROR_ERROR = 'Ошибка';
    const ERROR_MYSQL_CONNECTION = 'Нет подключения к серверу MySQL';
    const ERROR_MYSQL_QUERY = 'Не удалось выполнить запрос к базе данных';
    const ERROR_MYSQL_DETAILS = 'Ошибка %d: %s';
    const ERROR_MYSQL_CONNECTION_FILE_ERROR = 'Файл MYSQL_CONNECTION не найден или имеет неправильный формат';
    const ERROR_MYSQL_ZERO = 'Неверный запрос к базе данных';
    const ERROR_MYSQL_BAD_SCALE_NUM = 'Весы с номером %d в базе данных не найдены';
    const ERROR_MYSQL_MAX_LIMIT = 'Результат запроса превысил допустимые пределы<br>Попробуйте указать меньший период времени<br>и/или задайте параметры поиска.';

    const ERROR_JS_DISABLED = 'JavaScript отключен';
    const ERROR_JS_DISABLED_DETAILS = 'Для правильного функционирования данной страницы JavaScript должен быть разрешён';

    const ERROR_401_HEADER = 'Требуется аутентификация';
    const ERROR_401_SUB_HEADER = 'Для доступа к данному ресурсу требуется указать имя пользователя и пароль';

    const ERROR_XXX_SUB_HEADER = 'Но вы держитесь. Всего вам доброго, хорошего настроения и здоровья';

    const ERROR_403_HEADER = 'Доступ запрещён';
    const ERROR_403_SUB_HEADER = Strings::ERROR_XXX_SUB_HEADER;

    const ERROR_404_HEADER = 'Такой страницы нет';
    const ERROR_404_SUB_HEADER = Strings::ERROR_XXX_SUB_HEADER;

    const ERROR_412_HEADER = 'Неверный формат запроса';
    const ERROR_412_SUB_HEADER = 'При проверке на сервере одного или более полей заголовка запроса обнаружено несоответствие';

    const ERROR_500_HEADER = 'Внутренняя ошибка сервера';
    const ERROR_500_SUB_HEADER = Strings::ERROR_XXX_SUB_HEADER;

    const ERROR_INCOMPATIBLE_BROWSER_HEADER = 'Несовместимый браузер';
    const ERROR_INCOMPATIBLE_BROWSER_SUB_HEADER = Strings::ERROR_XXX_SUB_HEADER;

    const ERROR_GOTO_START = 'Перейти на начальную страницу';
    const ERROR_GOTO_START_COMPATIBLE = 'Перейти на начальную страницу в старом дизайне';

    const A_GOTO_MAIN = 'Список весовых';
    const A_GOTO_MAIN_COMPATIBLE = 'Список весовых в старом дизайне';
    const A_GOTO_MAIN_COMPATIBLE_WITH_DISABLED = 'Список весовых в старом дизайне, включая закрытые весы';
    const A_GOTO_WAC = 'Количество дней, отработанных без травм';
    const A_GOTO_DATETIME = 'Дата и время на компьютере';
    const A_GOTO_MYSQL_DATETIME = 'Дата и время на сервере MySQL';
    const A_GOTO_TEMP = 'Температура наружного воздуха';
    const A_GOTO_HELP = 'Справка';

    const A_TEXT_EXTERNAL_RESOURCES = 'Посторонние ресурсы';

    const A_GOTO_DATA_CENTER_ARM = 'Универсальный АРМ весоизмерения "Дата-Центр Автоматика"';
    const A_GOTO_CTA_AND_KIP_ASU_GAZ = 'Температура наружного воздуха, УТА';

    const HELP_TEXT_PARAMS = 'Параметры адресной строки';
    const HELP_TEXT_PARAMS_TEXT = 'Параметры вводятся в адресной строке браузера в конце адреса страницы. Началом запроса служит знак &laquo;?&raquo;, после которого добавляются параметры в виде <i>имя_параметра=значение_параметра</i>. Параметры разделяются символом &laquo;&amp;&raquo;.';
    const HELP_TEXT_PARAMS_LIST_HEADER = 'Список параметров';
    const HELP_TEXT_PARAMS_LIST_HEADER_1 = 'имя_параметра';
    const HELP_TEXT_PARAMS_LIST_HEADER_2 = 'значение_параметра';
    const HELP_TEXT_PARAMS_LIST_HEADER_3 = 'Описание';
    const HELP_TEXT_PARAMS_VALUE_BOOL = 'true|false';
    const HELP_TEXT_PARAMS_VALUE_INT = 'Целое число';
    const HELP_TEXT_PARAMS_NEW_DESIGN = 'Вывод сайта в новом дизайне';
    const HELP_TEXT_PARAMS_SHOW_METROLOGY = 'Вывод на главной странице метрологических параметров';
    const HELP_TEXT_PARAMS_SHOW_DISABLED = 'Вывод на главной странице скрытых (выведенных из эксплуатации) весов';
    const HELP_TEXT_PARAMS_USE_BACKUP = 'Использовать резервную базу данных';
    const HELP_TEXT_PARAMS_DEPARTMENT = 'Номер цеха на странице количества дней, отработанных без травм';
}
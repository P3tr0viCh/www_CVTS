<?php

class Strings
{
    const TAB = "  ";
    const SPACE = " ";

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

    const HEADER_PAGE_MAIN = 'ВЕСОИЗМЕРИТЕЛЬНЫЕ ТЕХНОЛОГИЧЕСКИЕ СИСТЕМЫ';
    const HEADER_PAGE_MAIN_BACKUP = 'РЕЗЕРВНЫЙ СЕРВЕР';
    const HEADER_PAGE_MAIN_LOGO_ALT = 'Металлоинвест | Уральская сталь';
    const HEADER_PAGE_MYSQL_DATETIME = 'Дата и время на сервере MySQL';
    const HEADER_PAGE_HELP = 'Справка';
    const HEADER_PAGE_A = 'Карта сайта';

    const DRAWER_TITLE = 'Весы';
    const DRAWER_START_PAGE = 'Общий список';
    const DRAWER_SHOW_ALL_TRAIN_SCALES = 'Все железнодорожные весы';
    const DRAWER_SHOW_VANLIST_QUERY = 'Информация по списку вагонов';
    const DRAWER_SHOW_IRON_QUERY = 'Провеска чугуна';
    const DRAWER_SHOW_IRON_CONTROL_QUERY = 'Контрольная провеска чугуна';
    const DRAWER_TITLE_SETTINGS = 'Настройки';
    const DRAWER_SHOW_DISABLED_ON = 'Показать скрытые весы';
    const DRAWER_SHOW_DISABLED_OFF = 'Не отображать скрытые весы';
    const DRAWER_SHOW_ALL_OPERATORS_ON = 'Показать весы всех операторов';
    const DRAWER_SHOW_ALL_OPERATORS_OFF = 'Не отображать весы посторонних операторов';
    const DRAWER_SHOW_METROLOGY_ON = 'Показать метрологические параметры';
    const DRAWER_SHOW_METROLOGY_OFF = 'Не отображать метрологические параметры';
    const DRAWER_A = 'Карта сайта';

    const FOOTER_LEFT_SECTION = 'МЕТАЛЛОИНВЕСТ, Уральская Сталь';
    const FOOTER_RIGHT_SECTION = '© Дураев Константин Петрович';

    const SHOW_ALL_TRAIN_SCALES = 'Сводная информация по всем железнодорожным весам';
    const SHOW_VANLIST_QUERY = 'Сводная информация по списку номеров вагонов';
    const SHOW_IRON_QUERY = 'Сводная информация по провескам чугуна';
    const SHOW_IRON_CONTROL_QUERY = 'Контрольная провеска чугуна';
    const SHOW_SLAG_CONTROL_QUERY = 'Контрольная провеска шлака';
    const SHOW_SENSORS_INFO_RESULT = 'Сводная информация по датчикам ВД-30';

    const SCALE_INFO_ALL_TRAIN_PLACE = 'ЖД весы';
    const SCALE_INFO_ALL_TRAIN_HEADER = 'Сводная информация по всем железнодорожным весам';
    const SCALE_INFO_VANLIST_PLACE = 'Список вагонов';
    const SCALE_INFO_VANLIST_PLACE_HEADER = 'Сводная информация по списку номеров вагонов';
    const SCALE_INFO_IRON_PLACE = 'Чугун';
    const SCALE_INFO_SLAG_PLACE = 'Шлак';
    const SCALE_INFO_IRON_HEADER = 'Провеска чугуна';
    const SCALE_INFO_IRON_CONTROL_HEADER = 'Контрольная провеска чугуна';
    const SCALE_INFO_SLAG_CONTROL_HEADER = 'Контрольная провеска шлака';
    const SCALE_INFO_SENSORS_INFO_PLACE = 'Датчики ВД-30';
    const SCALE_INFO_SENSORS_INFO_HEADER = 'Сводная информация по датчикам ВД-30';

    const SCALE_INFO_HEADER = '%s, весы №%d';

    const HEADER_INFO = 'Информация';
    const HEADER_DYNAMIC = 'Динамическое взвешивание';
    const HEADER_STATIC = 'Статическое взвешивание';
    const HEADER_RESULTS = 'Результаты взвешиваний';
    const HEADER_SERVICE = 'Сервис';
    const HEADER_PERIOD = 'Период';
    const HEADER_DATETIME_START = 'Начальная дата и время';
    const HEADER_DATETIME_END = 'Конечная дата и время';
    const HEADER_DATE_START = 'Начальная дата';
    const HEADER_DATE_END = 'Конечная дата';
    const HEADER_SEARCH = 'Поиск';
    const HEADER_SETTINGS = 'Настройки';
    const HEADER_COMPARE = 'Сравнение массы';

    const HEADER_LOADING = 'Загрузка. Подождите...';

    const HEADER_RESULT_VN_DYN_B = 'Динамическое взвешивание брутто';
    const HEADER_RESULT_VN_DYN_T = 'Динамическое взвешивание тары';
    const HEADER_RESULT_VN_STA_B = 'Статическое взвешивание брутто';
    const HEADER_RESULT_VN_STA_T = 'Статическое взвешивание тары';
    const HEADER_RESULT_TR_DYN = 'Динамическое взвешивание, составы';
    const HEADER_RESULT_TR_DYN_ONE = 'Провеска состава';
    const HEADER_RESULT_AUTO_B = 'Статическое взвешивание брутто';
    const HEADER_RESULT_AUTO_T = 'Статическое взвешивание тары';
    const HEADER_RESULT_KANAT = 'Список провесок';
    const HEADER_RESULT_DP = 'Список провесок';
    const HEADER_RESULT_DP_SUM = 'Сумма провесок';
    const HEADER_RESULT_CARGO_LIST = 'Список грузов';
    const HEADER_RESULT_CARGO_LIST_DYNAMIC = 'Список грузов (динамика)';
    const HEADER_RESULT_CARGO_LIST_STATIC = 'Список грузов (статика)';
    const HEADER_RESULT_COMPARE = 'Взвешивание вагонов, брутто';
    const HEADER_COEFFS = 'Коэффициенты';
    const SENSORS_ZEROS = 'Датчики, нули';
    const SENSORS_TEMPS = 'Датчики, температуры';
    const SENSORS_STATUS = 'Датчики, статус';
    const SENSORS_INFO = '';
    const HEADER_IRON = 'Сумма нетто посуточно';
    const HEADER_IRON_CONTROL = 'Сравнение массы';
    const HEADER_SLAG_CONTROL = 'Сравнение массы';
    const HEADER_VANLIST_WEIGHS = 'Взвешивание по всем весам';
    const HEADER_VANLIST_TARE = 'Последний вес тары по всем весам';

    const HEADER_RESULT_PERIOD_DATE = '%s %s';
    const HEADER_RESULT_PERIOD_FROM = '%s с %s';
    const HEADER_RESULT_PERIOD_TO = '%s с начала работы по %s';
    const HEADER_RESULT_PERIOD_FROM_TO = '%s с %s по %s';
    const HEADER_RESULT_PERIOD_ALL = '%s за всё время работы';
    const HEADER_RESULT_PERIOD_FROM_20_TO_20 = ' (начало суток с 20:00 предыдущего дня)';

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

    const TEXT_TABLE_CELL_EMPTY = '';

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
    const BUTTON_WEIGHS = 'Список провесок';
    const BUTTON_LAST_TARE = 'Последняя тара';
    const BUTTON_VIEW = 'Просмотр';
    const BUTTON_SUM_FOR_PERIOD = 'Сумма за период';
    const BUTTON_COEFFS = 'Коэффициенты';
    const BUTTON_SENSORS_ZEROS = 'Датчики, нули';
    const BUTTON_SENSORS_TEMPS = 'Датчики, температуры';
    const BUTTON_SENSORS_STATUS = 'Датчики, статус';
    const BUTTON_CLEAR = 'Очистить';

    const CHECKBOX_ALL_FIELDS = 'Подробные таблицы';
    const CHECKBOX_SHOW_CARGO_DATE = 'Время изменения рода груза';
//    const CHECKBOX_SHOW_DELTAS = 'Показать предельно допускаемые погрешности';
    const CHECKBOX_SHOW_DELTAS_MI_3115 = 'Предельные расхождения по МИ 3115';
    const CHECKBOX_SHOW_TOTAL_SUMS = 'Итоговые суммы';
    const CHECKBOX_COMPARE_FORWARD = 'Поиск «вперёд»';
    const CHECKBOX_COMPARE_BY_BRUTTO = 'Сравнивать брутто';
    const CHECKBOX_ONLY_CHARK = 'Только кокс';
    const CHECKBOX_DATETIME_ORDER_BY_ASC = 'Сортировка по дате от новых к старым';
    const CHECKBOX_DATETIME_FROM_20_TO_20 = 'Начало суток с 20:00 предыдущего дня';

    const MENU_DATES_CURRENT_DAY = 'Начало текущего дня';
    const MENU_DATES_CURRENT_MONTH = 'Начало текущего месяца';
    const MENU_DATES_CURRENT_WEEK = 'Начало текущей недели';
    const MENU_DATES_PREV_DAY = 'Прошлые сутки';
    const MENU_DATES_FROM_5_TO_5 = 'С 05:00 прошлого дня до 04:59 текущего';
    const MENU_DATES_FROM_20_TO_20 = 'С 20:00 позапрошлого дня до 19:59 прошлого';
    const MENU_DATES_CLEAR = 'Очистить';

    const MENU_COPY_ALL = 'Копировать всё';
    const MENU_COPY_TABLE = 'Копировать таблицу с заголовком';
    const MENU_COPY_TABLE_BODY = 'Копировать данные из таблицы';
    const MENU_COPY_TABLE_BODY_IRON_PREV_DAY = 'Копировать данные за прошлые сутки';
    const MENU_COPY_TABLE_BODY_IRON_PREV_3_DAY = 'Копировать данные за трое прошлых суток';

    const NAV_LINK_CLEAR = 'ОЧИСТИТЬ';
    const NAV_LINK_BACK = 'НАЗАД';
    const NAV_LINK_UPDATE = 'ОБНОВИТЬ';
    const NAV_LINK_SAVE = 'СОХРАНИТЬ';
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
    const INPUT_VANLIST = 'Список номеров вагонов';

    const HELP_DATE_OLD = '(день.месяц.год)<br>(дату полностью вводить не обязательно)<br>(если что-то не указано, подразумевается текущая дата)';
    const HELP_DATETIME_OLD = '(день.месяц.год час:минуты)<br>(дату или время полностью вводить не обязательно)<br>(если что-то не указано, подразумевается текущая дата)';
    const HELP_SEARCH = 'Символ «%» (процент) используется для замены группы символов, «_» (подчёркивание) – для замены одного символа';
    const HELP_SEARCH_OLD = '(используйте символ "%" для замены группы символов, "_" для замены одного символа)';
    const HELP_SCALES = 'Номера весов вводятся через запятую';
    const HELP_SCALES_OLD = '(номера весов вводятся через запятую)';

    const TEXT_SCALE_CLASS_STATIC = 'Статика';
    const TEXT_SCALE_CLASS_DYNAMIC = 'Динамика';
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

    const TEXT_SUM = "Сумма";
    const TEXT_AVG = "Среднее";
    const TEXT_TOTAL = "Итого";

    const TEXT_ON = "ON";
    const TEXT_OFF = "OFF";

    const TEXT_SENSORS_INFO_STATUS = "Статус";
    const TEXT_SENSORS_INFO_ZEROS_CURRENT = "Текущий ноль";
    const TEXT_SENSORS_INFO_ZEROS_INITIAL = "Исходный ноль";

    const TEXT_DELTA_MI_3115_OK = "Норма";

    const TEXT_NIGHT_MODE = 'ДЕНЬ/НОЧЬ';

    const TEXT_SITE_VERSION = 'Версия сайта: %s от %s';
    const TEXT_APACHE_VERSION = 'Версия Apache: %s';
    const TEXT_PHP_VERSION = 'Версия PHP: %s';

    const ERROR_ERROR = 'Ошибка';
    const ERROR_MYSQL_CONNECTION = 'Нет подключения к серверу MySQL';
    const ERROR_MYSQL_QUERY = 'Не удалось выполнить запрос к базе данных';
    const ERROR_MYSQL_DETAILS = 'Ошибка %d: %s';
    const ERROR_MYSQL_CONNECTION_FILE_ERROR = 'Файл MYSQL_CONNECTION не найден или имеет неправильный формат';
    const ERROR_MYSQL_BAD_SCALE_NUM = 'Весы с номером %d в базе данных не найдены';
    const ERROR_RESULT_MAX_ROWS = 'Результат запроса превысил допустимые пределы<br>Попробуйте указать меньший период времени<br>и/или задайте параметры поиска';
    const ERROR_RESULT_MAX_ROWS_DETAILS = 'Количество строк в результате: %d<br>Попытка открытия приведёт к зависанию браузера';

    const ERROR_JS_DISABLED = 'JavaScript отключен';
    const ERROR_JS_DISABLED_DETAILS = 'Для правильного функционирования данной страницы JavaScript должен быть разрешён';

    const ERROR_XXX_SUB_HEADER = 'Но вы держитесь. Всего вам доброго, хорошего настроения и здоровья';
    const ERROR_401_HEADER = 'Требуется аутентификация';
    const ERROR_401_SUB_HEADER = 'Для доступа к данному ресурсу требуется указать имя пользователя и пароль';
    const ERROR_403_HEADER = 'Доступ запрещён';
    const ERROR_403_SUB_HEADER = Strings::ERROR_XXX_SUB_HEADER;
    const ERROR_404_HEADER = 'Такой страницы нет';
    const ERROR_404_SUB_HEADER = Strings::ERROR_XXX_SUB_HEADER;
    const ERROR_412_HEADER = 'Неверный формат запроса';
    const ERROR_412_SUB_HEADER = 'При проверке на сервере одного или более полей запроса обнаружено несоответствие';
    const ERROR_500_HEADER = 'Внутренняя ошибка сервера';
    const ERROR_500_SUB_HEADER = Strings::ERROR_XXX_SUB_HEADER;
    const ERROR_530_HEADER = 'Недостаточно памяти для обработки запроса';
    const ERROR_530_SUB_HEADER = 'Попробуйте указать меньший период времени<br>и/или задайте параметры поиска';
    const ERROR_531_HEADER = 'Долгая обработка запроса';
    const ERROR_531_SUB_HEADER = 'Попробуйте указать меньший период времени<br>и/или задайте параметры поиска';

    const ERROR_INCOMPATIBLE_BROWSER_HEADER = 'Несовместимый браузер';

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
    const HELP_TEXT_PARAMS_DATE_FORMAT = 'Формат даты на странице количества дней, отработанных без травм<ul>
            <li>0 – Воскресенье, 29 марта</li>
            <li>1 – Вс, 29 марта</li>
            <li>2 – Вс, 29 мар</li>
            <li>3 – Вс, 29.03</li>
            </ul>';
}
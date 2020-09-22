const
    dayNames = ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"],
    dayNamesAbbr = ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
    monthNames = ["января", "февраля", "марта", "апреля", "мая", "июня",
        "июля", "августа", "сентября", "октября", "ноября", "декабря"],
    monthNamesAbbr = ["янв", "фев", "мар", "апр", "мая", "июн",
        "июл", "авг", "сен", "окт", "ноя", "дек"];

function getDayName(day, abbr) {
    return abbr ? dayNamesAbbr[day] : dayNames[day];
}

function getMonthName(month, abbr) {
    return abbr ? monthNamesAbbr[month] : monthNames[month];
}

function mySqlDateToDate(mySqlDateTime) {
    // MySql date format: 0000-00-00 00:00:00

    return new Date(
        parseInt(mySqlDateTime.substring(0, 4)),
        parseInt(mySqlDateTime.substring(5, 7)) - 1,
        parseInt(mySqlDateTime.substring(8, 10)));
}

function withZero(value) {
    return value > 9 ? value : "0" + value;
}

function getDateAsText(date, format) {
    switch (format) {
        case 1:
            // Вс, 29 марта
            return getDayName(date.getDay(), true) + ", " + date.getDate() + " " + getMonthName(date.getMonth());
        case 2:
            // Вс, 29 мар
            return getDayName(date.getDay(), true) + ", " + date.getDate() + " " + getMonthName(date.getMonth(), true);
        case 3:
            // Вс, 29.03
            return getDayName(date.getDay(), true) + ", " + withZero(date.getDate()) + "." + withZero(date.getMonth() + 1);
        default:
            // Воскресенье, 29 марта
            return getDayName(date.getDay(), false) + ", " + date.getDate() + " " + getMonthName(date.getMonth(), false);
    }
}
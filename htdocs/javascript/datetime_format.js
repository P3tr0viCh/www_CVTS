var
    dayNames = ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"],
    dayNamesAbbr = ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
    monthNames = ["января", "февраля", "марта", "апреля", "мая", "июня",
        "июля", "августа", "сентября", "октября", "ноября", "декабря"];

function getDayName(day, abbr) {
    return abbr ? dayNamesAbbr[day] : dayNames[day];
}

function getMonthName(month) {
    return monthNames[month];
}

function mySqlDateToDate(mySqlDateTime) {
    // MySql date format: 0000-00-00 00:00:00

    return new Date(
        mySqlDateTime.substring(0, 4),
        mySqlDateTime.substring(5, 7) - 1,
        mySqlDateTime.substring(8, 10));
}

function withZero(value) {
    return value > 9 ? value : "0" + value;
}
//noinspection JSUnusedGlobalSymbols
function startWAC(companyDateMySQL, departmentDateMySQL) {
    var
        MILLISECONDS_PER_DAY = 24 * 60 * 60 * 1000,

        currentTime,
        timeDelimiter,

        companyDate = mySqlDateToDate(companyDateMySQL),
        departmentDate = mySqlDateToDate(departmentDateMySQL),

        dateElement = document.getElementById('date'),
        timeElement = document.getElementById('time'),

        companyElement = document.getElementById('company'),
        departmentElement = document.getElementById('department'),

        now;

    updateDateTime();
    updateCounters();

    function dateDiff(date1, date2) {
        return Math.abs(Math.round((date1.getTime() - date2.getTime()) / MILLISECONDS_PER_DAY));
    }

    function setDateTimeTimer() {
        setTimeout(updateDateTime, 1000);
    }

    function setCountersTimer() {
        setTimeout(updateCounters, 1000);
    }

    function updateDateTime() {
        currentTime = new Date();

        setElementText(dateElement,
            getDayName(currentTime.getDay(), true) + ", " + currentTime.getDate() + " " + getMonthName(currentTime.getMonth()));

        timeDelimiter = ":";

        if (currentTime.getSeconds() % 2 === 0) {
            timeDelimiter = "<span class='color-text--darkgrey'>" + timeDelimiter + "</span>";
        }

        setElementText(timeElement,
            withZero(currentTime.getHours()) + timeDelimiter + withZero(currentTime.getMinutes()));

        setDateTimeTimer();
    }

    function setElementText(element, text) {
        element.innerHTML = text;
    }

    function updateCounters() {
        now = new Date();
        now.setHours(0);
        now.setMinutes(0);
        now.setSeconds(0);
        now.setMilliseconds(0);

        companyElement.innerHTML = dateDiff(now, companyDate);
        departmentElement.innerHTML = dateDiff(now, departmentDate);

        setCountersTimer();
    }
}
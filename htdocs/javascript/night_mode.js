let isNightMode = null;

//noinspection JSUnusedGlobalSymbols
function nightMode(nightMode) {
    if (nightMode === null) {
        checkNightMode();
    } else {
        setNightMode(nightMode);
    }

    function setTimer() {
        setTimeout(checkNightMode, 1000);
    }

    function isNight(datetime) {
        // for debug
        // var
        //     second = datetime.getSeconds();
        //
        // return (second > 10 && second < 20) || (second > 30 && second < 40) || (second > 50 && second < 60);

        const hour = datetime.getHours();

        return (hour >= 19) || (hour < 7);
    }

    function checkNightMode() {
        setNightMode(isNight(new Date()));

        setTimer();
    }

}

function setElementNightMode(element, nightMode) {
    if (nightMode) {
        addClass(element, "night");
    } else {
        removeClass(element, "night");
    }
}

function validTag(tagName) {
    tagName = tagName.toLowerCase();
    return tagName === "div" || tagName === "table" || tagName === "th" || tagName === "td" || tagName === "a";
}

function setNightMode(nightMode) {
    if (isNightMode === nightMode) return;

    isNightMode = nightMode;

    const elements = document.body.getElementsByTagName("*");

    setElementNightMode(document.body, nightMode);

    for (let i = 0, l = elements.length; i < l; i++) {
        if (validTag(elements[i].tagName)) {
            setElementNightMode(elements[i], nightMode);
        }
    }
}

//noinspection JSUnusedGlobalSymbols
function toggleNightMode() {
    setNightMode(!isNightMode);
}
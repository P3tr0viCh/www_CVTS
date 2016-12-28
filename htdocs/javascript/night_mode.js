var
    isNightMode = null,
    addClass,
    removeClass;

function addClassNew(e, c) {
    e.classList.add(c);
}

function removeClassNew(e, c) {
    e.classList.remove(c);
}

function containsClass(classNames, c) {
    var
        index = -1;

    for (var i = 0, l = classNames.length; i < l; i++) {
        if (classNames[i].toLowerCase() === c) {
            index = i;
            break;
        }
    }

    return index;
}

function addClassOld(e, c) {
    var
        className = e.className,
        classNames = e.className.split(" ");

    if (containsClass(classNames, c) !== -1) {
        return;
    }

    if (className !== '') {
        c = ' ' + c;
    }

    e.className = className + c;
}

function removeClassOld(e, c) {
    var
        classNames = e.className.split(" "),
        r = containsClass(classNames, c);

    if (r !== -1) {
        classNames.splice(r, 1);

        e.className = classNames.join(" ");
    }
}

//noinspection JSUnusedGlobalSymbols
function nightMode(nightMode) {
    if (typeof document.body.classList !== 'undefined') {
        addClass = addClassNew;
        removeClass = removeClassNew;
    } else {
        addClass = addClassOld;
        removeClass = removeClassOld;
    }

    if (nightMode === null) {
        checkNightMode();
    } else {
        setNightMode(nightMode);
    }

    function setTimer() {
        setTimeout(checkNightMode, 1000);
    }

    function isNight(datetime) {
        var
            hour = datetime.getHours();

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

    var
        elements = document.body.getElementsByTagName("*");

    setElementNightMode(document.body, nightMode);

    for (var i = 0, l = elements.length; i < l; i++) {
        if (validTag(elements[i].tagName)) {
            setElementNightMode(elements[i], nightMode);
        }
    }
}

//noinspection JSUnusedGlobalSymbols
function toggleNightMode() {
    setNightMode(!isNightMode);
}
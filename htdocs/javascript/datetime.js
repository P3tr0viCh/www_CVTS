//noinspection JSUnusedGlobalSymbols
function dateTime(showSeconds) {
    var currentTime,
        timeDelimiter,

        dateElement = document.getElementById('date'),
        timeElement = document.getElementById('time'),

        calcFontSizeElement = createCalcFontSizeElement(),
        timerId;

    if (window.addEventListener) {
        window.addEventListener("resize", onResize);
    } else {
        window.attachEvent("onresize", onResize);
    }

    updateDateTime();

    function onResize() {
        clearTimeout(timerId);

        setElementText(dateElement, "");
        setElementText(timeElement, "");

        dateElement.style.fontSize = 0;
        timeElement.style.fontSize = 0;

        updateDateTime();
    }

    function setTimer() {
        timerId = setTimeout(updateDateTime, 1000);
    }

    function updateDateTime() {
        currentTime = new Date();

        setElementText(dateElement,
            getDayName(currentTime.getDay(), false) + ", " + currentTime.getDate() + " " + getMonthName(currentTime.getMonth()));

        timeDelimiter = ":";

        if (!showSeconds && (currentTime.getSeconds() % 2 == 0)) {
            timeDelimiter = "<span class='color-text--darkgrey'>" + timeDelimiter + "</span>";
        }

        var
            time = withZero(currentTime.getHours()) + timeDelimiter + withZero(currentTime.getMinutes());

        if (showSeconds) {
            time += timeDelimiter + withZero(currentTime.getSeconds());
        }

        setElementText(timeElement, time);

        setTimer();
    }

    function setElementText(element, text) {
        if (element.innerHTML != text) {
            element.innerHTML = text;
            updateFontSize(element, text);
        }
    }

    function updateFontSize(element, text) {
        var
            height = element.clientHeight,
            width = element.clientWidth,
            paddingLeft,
            paddingRight;

        if (typeof getComputedStyle !== 'undefined') {
            paddingLeft = getComputedStyle(element).paddingLeft;
            paddingRight = getComputedStyle(element).paddingRight;
        } else {
            paddingLeft = element.currentStyle.paddingLeft;
            paddingRight = element.currentStyle.paddingRight;
        }
        paddingLeft = parseFloat(paddingLeft);
        paddingRight = parseFloat(paddingRight);

        width = width - paddingLeft - paddingRight;

        calcFontSizeElement.innerHTML = text;

        var
            fontSize = 10;
        for (var i = fontSize; i < height; i++) {
            calcFontSizeElement.style.fontSize = i + "px";

            if ((calcFontSizeElement.clientHeight > height) || (calcFontSizeElement.offsetWidth > width)) {
                break;
            }

            fontSize = i;
        }

        element.style.fontSize = fontSize + "px";
    }

    function createCalcFontSizeElement() {
        var element = document.createElement('div'),
            textNode = document.createTextNode("");

        element.appendChild(textNode);

        element.style.position = 'absolute';
        element.style.visibility = 'hidden';
        element.style.whiteSpace = 'nowrap';
        element.style.width = 'auto';
        element.style.height = 'auto';

        document.body.appendChild(element);

        return element;
    }
}
//noinspection JSUnusedGlobalSymbols
function dateTime(showSeconds) {
    const
        HEIGHT_TIME_MAGIC_NUMBER = 0,
        HEIGHT_DATE_MAGIC_NUMBER = 150;

    let currentTime,
        timeDelimiter,

        dateElement = document.getElementById('date'),
        timeElement = document.getElementById('time'),

        calcFontSizeElement = createCalcFontSizeElement(),

        timerId;

    if (window.addEventListener) {
        window.addEventListener("resize", onResize);
    } else {
        // noinspection JSUnresolvedFunction
        window.attachEvent("onresize", onResize);
    }

    updateDateTime();

    function onResize() {
        clearTimeout(timerId);

        setElementText(dateElement, "", HEIGHT_TIME_MAGIC_NUMBER);
        setElementText(timeElement, "", HEIGHT_DATE_MAGIC_NUMBER);

        dateElement.style.fontSize = '0';
        timeElement.style.fontSize = '0';

        updateDateTime();
    }

    function setTimer() {
        timerId = setTimeout(updateDateTime, 1000);
    }

    function updateDateTime() {
        currentTime = new Date();

        setElementText(dateElement, getDateAsText(currentTime, 0), HEIGHT_TIME_MAGIC_NUMBER);

        timeDelimiter = ":";

        if (!showSeconds && (currentTime.getSeconds() % 2 === 0)) {
            timeDelimiter = "<span class='color-text--darkgrey'>" + timeDelimiter + "</span>";
        }

        let time = withZero(currentTime.getHours()) + timeDelimiter + withZero(currentTime.getMinutes());

        if (showSeconds) {
            time += timeDelimiter + withZero(currentTime.getSeconds());
        }

        setElementText(timeElement, time, HEIGHT_DATE_MAGIC_NUMBER);

        setTimer();
    }

    function setElementText(element, text, magicNumber) {
        if (element.innerHTML !== text) {
            updateFontSize(element, text, magicNumber);

            element.innerHTML = text;
        }
    }

    function updateFontSize(element, text, magicNumber) {
        let clientWidth = element.clientWidth,
            clientHeight = element.clientHeight,
            paddingLeft,
            paddingRight;

        if (typeof getComputedStyle !== 'undefined') {
            paddingLeft = getComputedStyle(element).paddingLeft;
            paddingRight = getComputedStyle(element).paddingRight;
        } else {
            // noinspection JSUnresolvedVariable
            paddingLeft = element.currentStyle.paddingLeft;
            // noinspection JSUnresolvedVariable
            paddingRight = element.currentStyle.paddingRight;
        }
        paddingLeft = parseFloat(paddingLeft);
        paddingRight = parseFloat(paddingRight);

        clientWidth = clientWidth - paddingLeft - paddingRight;

        clientHeight = clientHeight - magicNumber;

        calcFontSizeElement.innerHTML = text;

        let fontSize = 10;
        for (let i = fontSize; i < clientHeight; i++) {
            calcFontSizeElement.style.fontSize = i + "px";

            if ((calcFontSizeElement.clientWidth > clientWidth) || (calcFontSizeElement.clientHeight > clientHeight)) {
                break;
            }

            fontSize = i;
        }

        calcFontSizeElement.innerHTML = null;

        element.style.fontSize = fontSize + "px";
    }

    function createCalcFontSizeElement() {
        const
            element = document.createElement('div'),
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
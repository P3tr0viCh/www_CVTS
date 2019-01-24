//noinspection JSUnusedGlobalSymbols
function temp() {
    var
        TEMP_URL = "temp.txt",

        DEG_TIMEOUT = 1000,
        UPDATE_TIMEOUT = 1000,
        // UPDATE_TIMEOUT = 5 * 60 * 1000,

        HEIGHT_MAGIC_NUMBER = 180; // TODO

    var
        currentTemp,

        degElement = null,
        tempElement = document.getElementById('temp'),

        hasError,
        xmlhttp = new XMLHttpRequest(),

        calcFontSizeElement = createCalcFontSizeElement(),

        timerUpdateId,
        timerDegId,

        degBlack = true;

    if (window.addEventListener) {
        window.addEventListener("resize", onResize);
    } else {
        // noinspection JSUnresolvedFunction
        window.attachEvent("onresize", onResize);
    }

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === XMLHttpRequest.DONE) {
            clearTimeout(timerDegId);

            hasError = xmlhttp.status !== 200;

            if (hasError) {
                currentTemp = xmlhttp.status;
            } else {
                // currentTemp = Math.floor(Math.random() * (50 - (-50) + 1)) + (-50);

                currentTemp = parseInt(xmlhttp.responseText, 10);

                hasError = isNaN(currentTemp);
            }

            if (hasError) {
                currentTemp = 'E: ' + currentTemp;

                addClass(tempElement, 'color-text--error');
            } else {
                currentTemp += degBlack ? "<span id='deg'>°</span>" : "<span id='deg' class='color-text--darkgrey'>°</span>";

                removeClass(tempElement, 'color-text--error');
            }

            setElementText(tempElement, currentTemp, HEIGHT_MAGIC_NUMBER);

            degElement = document.getElementById('deg');

            setTimer();
            setDegTimer();
        }
    };

    updateTemp();
    updateDeg();

    function onResize() {
        clearTimeout(timerUpdateId);
        clearTimeout(timerDegId);

        setElementText(tempElement, "", HEIGHT_MAGIC_NUMBER);

        tempElement.style.fontSize = '0';

        updateTemp();
        updateDeg();
    }

    function setTimer() {
        timerUpdateId = setTimeout(updateTemp, UPDATE_TIMEOUT);
    }

    function setDegTimer() {
        timerDegId = setTimeout(updateDeg, DEG_TIMEOUT);
    }

    function updateTemp() {
        xmlhttp.open("GET", TEMP_URL, true);
        xmlhttp.send();
    }

    function updateDeg() {
        if (degElement) {
            if (degBlack) {
                addClass(degElement, 'color-text--darkgrey');
            } else {
                removeClass(degElement, 'color-text--darkgrey');
            }

            degBlack = !degBlack;
        }

        setDegTimer();
    }

    function setElementText(element, text, magicNumber) {
        if (element.innerHTML !== text) {
            updateFontSize(element, text, magicNumber);

            element.innerHTML = text;
        }
    }

    function updateFontSize(element, text, magicNumber) {
        var
            clientWidth = element.clientWidth,
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

        var
            fontSize = 10;
        for (var i = fontSize; i < clientHeight; i++) {
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
//noinspection JSUnusedGlobalSymbols
function startWACTemp() {
    var
        TEMP_URL = "temp.txt",

        TEMP_TIMEOUT = 3000;
    // TEMP_TIMEOUT = 5 * 60 * 1000;

    var
        temp = null,
        currentTemp = null,
        prevTemp = null,

        timerTempId,

        tempElement = document.getElementById('temp'),

        xmlhttp = new XMLHttpRequest(),

        url = window.location.protocol + '//' + window.location.host + '/' + TEMP_URL;

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === XMLHttpRequest.DONE) {
            clearTimeout(timerTempId);

            if (xmlhttp.status === 200) {
                currentTemp = parseInt(xmlhttp.responseText, 10);

                if (isNaN(currentTemp)) {
                    currentTemp = null;
                }
            } else {
                currentTemp = null;
            }

            if (prevTemp === null) {
                prevTemp = currentTemp;
                temp = currentTemp;
            } else {
                if (currentTemp === null) {
                    temp = prevTemp;
                    prevTemp = null;
                } else {
                    if (currentTemp === 0) {
                        temp = prevTemp;
                        prevTemp = 0;
                    } else {
                        prevTemp = temp;
                        temp = currentTemp;
                    }
                }
            }

            setElementText(tempElement, temp !== null ? temp + '°' : '');

            setTempTimer();
        }
    };

    updateTemp();

    function setTempTimer() {
        timerTempId = setTimeout(updateTemp, TEMP_TIMEOUT);
    }

    function updateTemp() {
        xmlhttp.open("GET", url, true);
        xmlhttp.send();
    }

    function setElementText(element, text) {
        element.innerHTML = text;
    }
}
//noinspection JSUnusedGlobalSymbols
function startWACTemp() {
    var
        // TEMP_URL = "temp.txt",

        TEMP_TIMEOUT = 1000;
    // TEMP_TIMEOUT = 5 * 60 * 1000;

    var
        currentTemp,

        tempElement = document.getElementById('temp');

    updateTemp();

    function setTempTimer() {
        setTimeout(updateTemp, TEMP_TIMEOUT);
    }

    function updateTemp() {
        currentTemp = Math.floor(Math.random() * (50 - (-50) + 1)) + (-50);

        currentTemp += '°';

        setElementText(tempElement, currentTemp);

        setTempTimer();
    }

    function setElementText(element, text) {
        element.innerHTML = text;
    }
}
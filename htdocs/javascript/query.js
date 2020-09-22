// Используется в query.php

const inputs = [];

const resultTypes = [];

//noinspection JSUnusedGlobalSymbols
function setInputs() {
    for (let i = 0, l = arguments.length; i < l; i++) {
        inputs[i] = document.getElementById(arguments[i]);
    }
}

//noinspection JSUnusedGlobalSymbols
function setResultTypes() {
    for (let i = 0, l = arguments.length; i < l; i++) {
        resultTypes[i] = arguments[i];
    }
}

//noinspection JSUnusedGlobalSymbols
function setDates(value) {
    const values = ["", "", "", "", "", "", "", "", "", ""];

    const currDate = new Date();
    currDate.setHours(0, 0, 0, 0);

    switch (value) {
        case "startCurrentMonth":
            values[0] = "1";
            values[1] = currDate.getMonth() + 1;
            values[2] = currDate.getFullYear() + "";
            values[3] = "00";
            values[4] = "00";

            break;
        case "startCurrentDay":
            values[0] = currDate.getDate() + "";
            values[1] = currDate.getMonth() + 1;
            values[2] = currDate.getFullYear() + "";
            values[3] = "00";
            values[4] = "00";

            break;
        case "startCurrentWeek":
            let day = currDate.getDay();
            day === 0 ? day = 6 : day--;
            currDate.setDate(currDate.getDate() - day);
            values[0] = currDate.getDate() + "";
            values[1] = currDate.getMonth() + 1;
            values[2] = currDate.getFullYear() + "";
            values[3] = "00";
            values[4] = "00";

            break;
        case "prevDay":
            currDate.setDate(currDate.getDate() - 1);
            values[0] = currDate.getDate() + "";
            values[1] = currDate.getMonth() + 1;
            values[2] = currDate.getFullYear() + "";
            values[3] = "00";
            values[4] = "00";

            values[5] = currDate.getDate() + "";
            values[6] = currDate.getMonth() + 1;
            values[7] = currDate.getFullYear() + "";
            values[8] = "23";
            values[9] = "59";

            break;
        case "from5to5":
            values[5] = currDate.getDate() + "";
            values[6] = currDate.getMonth() + 1;
            values[7] = currDate.getFullYear() + "";
            values[8] = "04";
            values[9] = "59";

            currDate.setDate(currDate.getDate() - 1);
            values[0] = currDate.getDate() + "";
            values[1] = currDate.getMonth() + 1;
            values[2] = currDate.getFullYear() + "";
            values[3] = "05";
            values[4] = "00";

            break;
        case "from20to20":
            currDate.setDate(currDate.getDate() - 1);
            values[5] = currDate.getDate() + "";
            values[6] = currDate.getMonth() + 1;
            values[7] = currDate.getFullYear() + "";
            values[8] = "19";
            values[9] = "59";

            currDate.setDate(currDate.getDate() - 1);
            values[0] = currDate.getDate() + "";
            values[1] = currDate.getMonth() + 1;
            values[2] = currDate.getFullYear() + "";
            values[3] = "20";
            values[4] = "00";

            break;
    }

    for (let i = 0; i < values.length; i++) {
        if (inputs[i]) {
            inputs[i].value = values[i];
        }
    }

    checkTextFields();

    saveInputs();
}

function checkTextFields() {
    const texts = document.querySelectorAll('.mdl-js-textfield');
    if (texts) {
        for (let i = 0, l = texts.length; i < l; i++) {
            // noinspection JSUnresolvedFunction
            texts[i].MaterialTextfield.checkDirty();
        }
    }
}

function checkSwitches() {
    const switches = document.querySelectorAll('.mdl-js-switch');
    if (switches) {
        for (let i = 0, l = switches.length; i < l; i++) {
            switches[i].MaterialSwitch.checkToggleState();
        }
    }
}

// noinspection JSUnusedGlobalSymbols
function formReset() {
    const form = document.getElementById("formResult");

    form.reset();

    checkTextFields();
    checkSwitches();
}

// noinspection JSUnusedGlobalSymbols
function updateInputs() {
    let value, element;

    for (let i = 0, l = inputs.length; i < l; i++) {
        if ((element = inputs[i]) && (value = getCookie(element.id))) {
            if (element.type === 'checkbox') {
                element.checked = value === 'on';
            } else {
                element.value = value;
            }
        }
    }
}

// noinspection JSUnusedGlobalSymbols
function saveInputs() {
    let element;

    let i, l;

    for (i = 0, l = inputs.length; i < l; i++) {
        element = inputs[i];

        if (element) {
            if (element.type === 'checkbox') {
                if (element.checked) {
                    setCookie(element.id, 'on');
                } else {
                    setCookie(element.id, 'off');
                }
            } else {
                if (element.value) {
                    setCookie(element.id, element.value);
                } else {
                    deleteCookie(element.id);
                }
            }
        }
    }

    const inputsList = document.getElementsByTagName('input');
    for (i = 0, l = inputsList.length; i < l; i++) {
        if (inputsList[i].type === 'hidden') {
            element = document.getElementById(inputsList[i].name);

            if (element) {
                inputsList[i].value = element.value;
            }

            if (!inputsList[i].value) {
                // Запрет передачи пустых значений из полей ввода
                inputsList[i].disabled = true;
            } else {
                inputsList[i].value = encodeURIComponent(inputsList[i].value);
            }
        }
    }
}

// noinspection JSUnusedGlobalSymbols
function clearInputs() {
    let element;

    for (let i = 0, l = inputs.length; i < l; i++) {
        element = inputs[i];

        if (element) {
            deleteCookie(element.id);
        }
    }
}

// noinspection JSUnusedGlobalSymbols
function onButtonClick(value) {
    document.getElementById('result_type_hidden').value = value;

    saveInputs();

    document.getElementById("formResult").submit();
}
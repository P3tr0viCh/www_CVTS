// Используется в query.php

var inputs = [];

//noinspection JSUnusedGlobalSymbols
function setInputs() {
    for (var i = 0, l = arguments.length; i < l; i++) {
        inputs[i] = document.getElementById(arguments[i]);
    }
}

//noinspection JSUnusedGlobalSymbols
function setDates(value) {
    var values = ["", "", "", "", "", "", "", "", "", ""];

    var currDate = new Date();
    currDate.setHours(0, 0, 0, 0);

    switch (value) {
        case "startCurrentMonth":
            values[0] = "1";
            values[1] = currDate.getMonth() + 1;
            values[2] = currDate.getFullYear();
            values[3] = "00";
            values[4] = "00";

            break;
        case "startCurrentDay":
            values[0] = currDate.getDate();
            values[1] = currDate.getMonth() + 1;
            values[2] = currDate.getFullYear();
            values[3] = "00";
            values[4] = "00";

            break;
        case "startCurrentWeek":
            var day = currDate.getDay();
            day === 0 ? day = 6 : day--;
            currDate.setDate(currDate.getDate() - day);
            values[0] = currDate.getDate();
            values[1] = currDate.getMonth() + 1;
            values[2] = currDate.getFullYear();
            values[3] = "00";
            values[4] = "00";

            break;
        case "prevDay":
            currDate.setDate(currDate.getDate() - 1);
            values[0] = currDate.getDate();
            values[1] = currDate.getMonth() + 1;
            values[2] = currDate.getFullYear();
            values[3] = "00";
            values[4] = "00";

            values[5] = currDate.getDate();
            values[6] = currDate.getMonth() + 1;
            values[7] = currDate.getFullYear();
            values[8] = "23";
            values[9] = "59";

            break;
        case "from5to5":
            values[5] = currDate.getDate();
            values[6] = currDate.getMonth() + 1;
            values[7] = currDate.getFullYear();
            values[8] = "04";
            values[9] = "59";

            currDate.setDate(currDate.getDate() - 1);
            values[0] = currDate.getDate();
            values[1] = currDate.getMonth() + 1;
            values[2] = currDate.getFullYear();
            values[3] = "05";
            values[4] = "00";

            break;
        case "from20to20":
            values[5] = currDate.getDate();
            values[6] = currDate.getMonth() + 1;
            values[7] = currDate.getFullYear();
            values[8] = "19";
            values[9] = "59";

            currDate.setDate(currDate.getDate() - 1);
            values[0] = currDate.getDate();
            values[1] = currDate.getMonth() + 1;
            values[2] = currDate.getFullYear();
            values[3] = "20";
            values[4] = "00";

            break;
    }

    for (var i = 0; i < values.length; i++) {
        inputs[i].value = values[i];
    }

    checkTextFields();
}

function checkTextFields() {
    var texts = document.querySelectorAll('.mdl-js-textfield');
    if (texts) {
        for (var i = 0, l = texts.length; i < l; i++) {
            texts[i].MaterialTextfield.checkDirty();
        }
    }
}

function checkSwitches() {
    var switches = document.querySelectorAll('.mdl-js-switch');
    if (switches) {
        for (var i = 0, l = switches.length; i < l; i++) {
            switches[i].MaterialSwitch.checkToggleState();
        }
    }
}

// noinspection JSUnusedGlobalSymbols
function formReset() {
    var form = document.getElementById("formResult");

    form.reset();

    checkTextFields();
    checkSwitches();
}

// noinspection JSUnusedGlobalSymbols
function updateInputs() {
    var value, element;

    for (var i = 0, l = inputs.length; i < l; i++) {
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
    var element;

    var i, l;

    for (i = 0, l = inputs.length; i < l; i++) {
        if (element = inputs[i]) {
            if (element.type === 'checkbox') {
                if (element.checked) {
                    setCookie(element.id, 'on');
                } else {
                    deleteCookie(element.id);
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

    var inputsList = document.getElementsByTagName('input');
    for (i = 0, l = inputsList.length; i < l; i++) {
        if (inputsList[i].type === 'text' || inputsList[i].type === 'search') {
            inputsList[i].classList.add('input-color--wait');
        }

        if (!inputsList[i].value) {
            // Запрет передачи пустых значений из полей ввода
            inputsList[i].disabled = true;
        } else {
            if (inputsList[i].type === 'text' || inputsList[i].type === 'search') {
                inputsList[i].value = encodeURIComponent(inputsList[i].value);
            }
        }
    }
}

// noinspection JSUnusedGlobalSymbols
function clearInputs() {
    var element;

    for (var i = 0, l = inputs.length; i < l; i++) {
        if (element = inputs[i]) {
            deleteCookie(element.id);
        }
    }
}
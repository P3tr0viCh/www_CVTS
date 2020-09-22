// Используется в result.php

let numRows;

function elementSetEnabled(elementId) {
    const element = document.getElementById(elementId);
    if (element) element.removeAttribute("disabled");
}

//noinspection JSUnusedGlobalSymbols
function showContent() {
    document.getElementById("divLoading").style.display = "none";
    document.getElementById("divContent").style.display = "block";

    document.getElementById("menuMoreList").classList.remove("hidden");

    if (document.forms["formExcel"] !== null && numRows > 0) {
        document.getElementById("saveIcon").classList.remove("hidden");
        document.getElementById("saveText").classList.remove("hidden");
    }

    if (numRows > 0) {
        elementSetEnabled("copyAll");
        elementSetEnabled("copyTable");
        elementSetEnabled("copyTableBody");

        if (numRows > 1) {
            elementSetEnabled("copyTableBodyIronPrevDay");
        }
        if (numRows > 3) {
            elementSetEnabled("copyTableBodyIronPrev3Day");
        }
    }
}

//noinspection JSUnusedGlobalSymbols
function copyToClipboard(copy) {
    setTimeout(function () {
        const text = createTextForCopy(copy);
        copyTextToClipboard(text);
    }, 100);
}

function createTextForCopy(copy) {
    let row, cell, rowLength, cellLength,
        rowStart, rowEnd, cellStart,
        tableRow,
        element,
        result = "",
        i, l;

    //noinspection FallThroughInSwitchStatementJS
    switch (copy) {
        case 'all':
            element = document.querySelectorAll("[data-header]");

            for (i = 0, l = element.length; i < l; i++) {
                result = result + element[i].textContent + '\n';
            }
        case 'table':
            element = document.getElementById("tableHead");

            for (row = 0, rowLength = element.rows.length; row < rowLength; row++) {
                tableRow = element.rows[row];

                for (cell = 0, cellLength = tableRow.cells.length; cell < cellLength; cell++) {
                    result = result + tableRow.cells[cell].textContent;

                    for (i = 0, l = tableRow.cells[cell].colSpan; i < l; i++) {
                        result = result + '\t';
                    }
                }

                // Удаление последнего символа табуляции
                result = result.slice(0, -1) + '\n';
            }
        case 'tableBody':
        case 'tableBodyIronPrevDay':
        case 'tableBodyIronPrev3Day':
            element = document.getElementById("tableBody");
            rowLength = element.rows.length;

            switch (copy) {
                case 'tableBodyIronPrevDay':
                    rowStart = rowLength - 2;
                    rowEnd = rowLength - 1;
                    cellStart = 1;
                    break;
                case 'tableBodyIronPrev3Day':
                    rowStart = rowLength - 4;
                    rowEnd = rowLength - 1;
                    cellStart = 1;
                    break;
                default:
                    rowStart = 0;
                    rowEnd = rowLength;
                    cellStart = 0;
            }

            for (row = rowStart; row < rowEnd; row++) {
                tableRow = element.rows[row];

                for (cell = cellStart, cellLength = tableRow.cells.length; cell < cellLength; cell++) {
                    result = result + tableRow.cells[cell].textContent;

                    for (i = 0, l = tableRow.cells[cell].colSpan; i < l; i++) {
                        result = result + '\t';
                    }
                }

                result = result.slice(0, -1) + '\n';
            }
    }

    // console.log(result);

    return result;
}

function copyTextToClipboard(text) {
    // console.log(text);

    const textArea = document.createElement("textarea");

    textArea.style.position = 'fixed';

    textArea.style.cursor = 'progress';

    textArea.style.left = '0';
    textArea.style.top = '0';

    textArea.style.zIndex = '1';

    textArea.style.width = '100%';
    textArea.style.height = '100%';

    textArea.style.margin = '0';
    textArea.style.padding = '0';

    textArea.style.border = 'none';
    textArea.style.resize = 'none';
    textArea.style.outline = 'none';
    textArea.style.overflow = 'hidden';
    textArea.style.boxShadow = 'none';

    textArea.style.background = 'black';
    textArea.style.opacity = '0.3';
    textArea.style.color = 'transparent';

    textArea.readOnly = true;

    textArea.value = text;

    document.body.appendChild(textArea);

    textArea.focus();

    textArea.select();

    try {
        if (!document.execCommand('copy')) {
            console.log('execCommand: unable to copy');
        }
    } catch (err) {
        console.log('catch: unable to copy');
    }

    textArea.selectionStart = textArea.selectionEnd;

    document.body.removeChild(textArea);
}

//noinspection JSUnusedGlobalSymbols
function saveToExcel() {
    document.forms['formExcel'].submit();
    return false;
}
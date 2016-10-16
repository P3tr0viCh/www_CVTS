var hasData; // if ($numRows > 0)

// »спользуетс€ в result.php
//noinspection JSUnusedGlobalSymbols
function showContent() {
    document.getElementById("divLoading").style.display = "none";
    document.getElementById("divContent").style.display = "block";

    document.getElementById("menuMoreList").classList.remove("hidden");

    if (document.forms["formExcel"] != null) {
        document.getElementById("saveIcon").classList.remove("hidden");
        document.getElementById("saveText").classList.remove("hidden");
    }

    if (hasData) {
        document.getElementById("copyAll").removeAttribute("disabled");
        document.getElementById("copyTable").removeAttribute("disabled");
        document.getElementById("copyTableBody").removeAttribute("disabled");
    }
}

//noinspection JSUnusedGlobalSymbols
function copyToClipboard(copy) {
    setTimeout(function () {
        var text = createTextForCopy(copy);
        copyTextToClipboard(text);
    }, 100);
}

function createTextForCopy(copy) {
    var row, cell, rowLength, cellLength,
        tableRow,
        textHeader = "",
        textTableHead = "",
        textTableBody = "";

    //noinspection FallThroughInSwitchStatementJS
    switch (copy) {
        case 'all':
            var header = document.querySelectorAll("[data-header]");

            for (var i = 0, l = header.length; i < l; i++) {
                textHeader = textHeader + header[i].textContent + '\n';
            }
        case 'table':
            var tableHead = document.getElementById("tableHead");

            for (row = 0, rowLength = tableHead.rows.length; row < rowLength; row++) {
                tableRow = tableHead.rows[row];

                for (cell = 0, cellLength = tableRow.cells.length; cell < cellLength; cell++) {
                    textTableHead = textTableHead + tableRow.cells[cell].textContent;

                    for (i = 0, l = tableRow.cells[cell].colSpan; i < l; i++) {
                        textTableHead = textTableHead + '\t';
                    }
                }

                // ”даление последнего символа табул€ции
                textTableHead = textTableHead.slice(0, -1) + '\n';
            }
        case 'tableBody':
            var tableBody = document.getElementById("tableBody");

            for (row = 0, rowLength = tableBody.rows.length; row < rowLength; row++) {
                tableRow = tableBody.rows[row];

                for (cell = 0, cellLength = tableRow.cells.length; cell < cellLength; cell++) {
                    textTableBody = textTableBody + tableRow.cells[cell].textContent + '\t';
                }

                textTableBody = textTableBody.slice(0, -1) + '\n';
            }
    }

    // console.log(textHeader + textTableHead + textTableBody);

    return textHeader + textTableHead + textTableBody;
}

function copyTextToClipboard(text) {
    // console.log(text);

    var textArea = document.createElement("textarea");

    textArea.style.position = 'fixed';

    textArea.style.cursor = 'progress';

    textArea.style.left = 0;
    textArea.style.top = 0;

    textArea.style.zIndex = 1;

    textArea.style.width = '100%';
    textArea.style.height = '100%';

    textArea.style.margin = 0;
    textArea.style.padding = 0;

    textArea.style.border = 'none';
    textArea.style.resize = 'none';
    textArea.style.outline = 'none';
    textArea.style.overflow = 'hidden';
    textArea.style.boxShadow = 'none';

    textArea.style.background = 'black';
    textArea.style.opacity = 0.3;
    textArea.style.color = 'transparent';

    textArea.readOnly = true;

    textArea.value = text;

    document.body.appendChild(textArea);

    textArea.focus();

    textArea.select();

    try {
        if (!document.execCommand('copy')) {
            console.log('Unable to copy');
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
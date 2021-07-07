// Функции используются в query.php, result.php

//noinspection JSUnusedGlobalSymbols
function reloadData() {
    window.location.reload(true);
}

//noinspection JSUnusedGlobalSymbols
function goBack() {
    window.history.back();
}

//noinspection JSUnusedGlobalSymbols
function goUrl(url, blank) {
    if (blank) blank = "_blank";
    window.open(url, blank);
}

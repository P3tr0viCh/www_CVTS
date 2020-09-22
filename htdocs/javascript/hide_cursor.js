const HIDE_CURSOR_TIMEOUT = 3000;

let mouseTimer = null, cursorVisible = true;

//noinspection JSUnusedGlobalSymbols
function startHideCursor() {
    startTimer();
}

function hideCursor() {
    mouseTimer = null;
    document.body.style.cursor = "url('/images/none.cur'), auto";
    cursorVisible = false;
}

function showCursor() {
    document.body.style.cursor = "default";
    cursorVisible = true;
}

function startTimer() {
    mouseTimer = window.setTimeout(hideCursor, HIDE_CURSOR_TIMEOUT);
}

function mouseMove() {
    if (mouseTimer) {
        window.clearTimeout(mouseTimer);
    }

    if (!cursorVisible) {
        showCursor();
    }

    startTimer();
}

document.onmousemove = function () {
    mouseMove();
};
var
    addClass,
    removeClass;

function addClassNew(e, c) {
    e.classList.add(c);
}

function removeClassNew(e, c) {
    e.classList.remove(c);
}

function containsClass(classNames, c) {
    var
        index = -1;

    for (var i = 0, l = classNames.length; i < l; i++) {
        if (classNames[i].toLowerCase() === c) {
            index = i;
            break;
        }
    }

    return index;
}

function addClassOld(e, c) {
    var
        className = e.className,
        classNames = e.className.split(" ");

    if (containsClass(classNames, c) !== -1) {
        return;
    }

    if (className !== '') {
        c = ' ' + c;
    }

    e.className = className + c;
}

function removeClassOld(e, c) {
    var
        classNames = e.className.split(" "),
        r = containsClass(classNames, c);

    if (r !== -1) {
        classNames.splice(r, 1);

        e.className = classNames.join(" ");
    }
}

//noinspection JSUnusedGlobalSymbols
function initClassUtils() {
    if (typeof document.body.classList !== 'undefined') {
        addClass = addClassNew;
        removeClass = removeClassNew;
    } else {
        addClass = addClassOld;
        removeClass = removeClassOld;
    }
}
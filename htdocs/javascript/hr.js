/*
 * Установка ширины разделителя (div.hr).
 *
 * Ширина устанавливается по ширине таблицы, если она больше ширины экрана.
 *
 * В конце BODY необходимо вызвать функцию updateHRWidthOnEndBody() для совместимости с IE8 и ниже.
 */

if (document.addEventListener) {
    document.addEventListener("DOMContentLoaded", updateHRWidth);
}

//noinspection JSUnusedGlobalSymbols
function updateHRWidthOnEndBody() {
    if (!document.querySelectorAll) {
        // noinspection JSValidateTypes
        document.querySelectorAll = function (selectors) {
            var style = document.createElement('style'), elements = [], element;
            document.documentElement.firstChild.appendChild(style);
            document._qsa = [];

            // noinspection JSDeprecatedSymbols
            style.styleSheet.cssText = selectors +
                '{x-qsa:expression(document._qsa && document._qsa.push(this))}';
            window.scrollBy(0, 0);
            style.parentNode.removeChild(style);

            while (document._qsa.length) {
                element = document._qsa.shift();
                element.style.removeAttribute('x-qsa');
                elements.push(element);
            }
            document._qsa = null;

            return elements;
        };
    }

    if (!document.addEventListener) {
        updateHRWidth();
    }
}


// Используется в result.php
//noinspection JSUnusedGlobalSymbols
function updateHRWidth() {
    var
        table = document.getElementById('tableBody');

    if (!table) {
        return;
    }

    var hr = document.querySelectorAll("div.hr"),
        windowWidth = window.innerWidth || document.documentElement.clientWidth,
        bodyWidth = table.offsetWidth,
        elementContent = document.getElementById("divContent"),
        marginLeft,
        marginRight;

    if (typeof getComputedStyle !== 'undefined') {
        marginLeft = getComputedStyle(elementContent).marginLeft;
        marginRight = getComputedStyle(elementContent).marginRight;
    } else {
        // noinspection JSUnresolvedVariable
        marginLeft = elementContent.currentStyle.marginLeft;
        // noinspection JSUnresolvedVariable
        marginRight = elementContent.currentStyle.marginRight;
    }

    marginLeft = parseFloat(marginLeft);
    marginRight = parseFloat(marginRight);

    var hrWidth = marginLeft + bodyWidth + marginRight;

    if (hrWidth > windowWidth) {
        for (var i = 0, l = hr.length; i < l; i++) {
            hr[i].style.width = hrWidth + 'px';
        }
    }
}
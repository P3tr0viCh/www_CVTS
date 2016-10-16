/*
 * Установка ширины разделителя (div.hr).
 *
 * Ширина устанавливается по ширине контента, если она больше ширины экрана.
 *
 * В конце BODY необходимо добавить <script>updateHRWidthOnEndBody();</script> для совместимости с IE8.
 */

if (document.addEventListener) {
    document.addEventListener("DOMContentLoaded", updateHRWidth);
}

//noinspection JSUnusedGlobalSymbols
function updateHRWidthOnEndBody() {
    if (!document.addEventListener) {
        updateHRWidth();
    }
}

// Используется в result.php
//noinspection JSUnusedGlobalSymbols
function updateHRWidth() {
    var hr = document.querySelectorAll("div.hr"),
        windowWidth = window.innerWidth || document.documentElement.clientWidth,
        bodyWidth = document.getElementById('tableBody').offsetWidth,
        elementContent = document.getElementById("divContent"),
        marginLeft,
        marginRight;

    if (typeof getComputedStyle !== 'undefined') {
        marginLeft = getComputedStyle(elementContent).marginLeft;
        marginRight = getComputedStyle(elementContent).marginRight;
    } else {
        marginLeft = elementContent.currentStyle.marginLeft;
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
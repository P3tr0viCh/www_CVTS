/*
 * Установка положения футтера.
 *
 * Если высота контента мала, футтер устанавливается в низ страницы.
 * Если высота большая, футтер устанавливается в конце контента.
 *
 * Страница должна содержать три элемента:
 * элемент, содержащий хизер, с айди divHeader;
 * элемент, содержащий основной контент, с айди divContent;
 * элемент, содержащий футтер, с айди divFooter.
 *
 * Положение футтера изменяется за счёт минимальной высоты контента.
 *
 * В конце BODY необходимо добавить <script>updateContentMinHeightOnEndBody();</script> для совместимости с IE8.
 */

if (document.addEventListener) {
    document.addEventListener("DOMContentLoaded", updateContentMinHeight);
}

if (window.addEventListener) {
    window.addEventListener("resize", updateContentMinHeight);
} else {
    window.attachEvent("onresize", updateContentMinHeight);
}

//noinspection JSUnusedGlobalSymbols
function updateContentMinHeightOnEndBody() {
    if (!document.addEventListener) {
        updateContentMinHeight();
    }
}

function updateContentMinHeight() {
    var windowHeight = window.innerHeight || document.documentElement.clientHeight,
        headerHeight = document.getElementById("divHeader").clientHeight,
        footerHeight = document.getElementById("divFooter").clientHeight,
        elementContent = document.getElementById("divContent"),
        marginTop,
        marginBottom;

    if (typeof getComputedStyle !== 'undefined') {
        marginTop = getComputedStyle(elementContent).marginTop;
        marginBottom = getComputedStyle(elementContent).marginBottom;
    } else {
        marginTop = elementContent.currentStyle.marginTop;
        marginBottom = elementContent.currentStyle.marginBottom;
    }

    marginTop = parseFloat(marginTop);
    marginBottom = parseFloat(marginBottom);

    var minHeight = windowHeight - headerHeight - footerHeight - marginTop - marginBottom;

    // console.log("windowHeight == " + windowHeight + ", headerHeight == " + headerHeight +
    //     ", footerHeight == " + footerHeight + ", margins == " + (marginTop + marginBottom) +
    //     ", minHeight == " + minHeight);

    elementContent.style.minHeight = minHeight + "px";
}
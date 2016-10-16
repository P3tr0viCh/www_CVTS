/*
 * ��������� ��������� �������.
 *
 * ���� ������ �������� ����, ������ ��������������� � ��� ��������.
 * ���� ������ �������, ������ ��������������� � ����� ��������.
 *
 * �������� ������ ��������� ��� ��������:
 * �������, ���������� �����, � ���� divHeader;
 * �������, ���������� �������� �������, � ���� divContent;
 * �������, ���������� ������, � ���� divFooter.
 *
 * ��������� ������� ���������� �� ���� ����������� ������ ��������.
 *
 * � ����� BODY ���������� �������� <script>updateContentMinHeightOnEndBody();</script> ��� ������������� � IE8.
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
    console.log("updateContentMinHeight");
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
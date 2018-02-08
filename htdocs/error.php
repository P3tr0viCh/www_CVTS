<?php
require_once "include/Constants.php";
require_once "include/Strings.php";
require_once "include/Functions.php";

require_once "include/CheckBrowser.php";

require_once "include/echo_html_page.php";

use Strings as S;

$newDesign = isNewDesign(true);

$errorNum = (isset($_SERVER["QUERY_STRING"]) && ctype_digit($_SERVER["QUERY_STRING"])) ?
    (int)$_SERVER["QUERY_STRING"] : 500;

switch ($errorNum) {
    case 401:
        $errorHeader = S::ERROR_401_HEADER;
        $errorSubHeader = S::ERROR_401_SUB_HEADER;
        break;
    case 403:
        $errorHeader = S::ERROR_403_HEADER;
        $errorSubHeader = S::ERROR_403_SUB_HEADER;
        break;
    case 404:
        $errorHeader = S::ERROR_404_HEADER;
        $errorSubHeader = S::ERROR_404_SUB_HEADER;
        break;
    case 500:
    default:
        $errorNum = 500;
        $errorHeader = S::ERROR_500_HEADER;
        $errorSubHeader = S::ERROR_500_SUB_HEADER;
}

echoStartPage();

$styles[] = "/styles/error_common.css";
if ($newDesign) {
    $styles[] = "/styles/error.css";
    $styles[] = "/fonts/roboto/roboto.css";
} else {
    $styles[] = "/styles/error_compat.css";
}

echoHead($newDesign, $errorNum, $styles, null, null, false);

echoStartBody($newDesign);

echo '<div class="div-center-outer--center">' . PHP_EOL;
echo S::TAB;
echo '<div class="div-center-middle">' . PHP_EOL;
echo S::TAB . S::TAB;
echo '<div class="div-center-inner">' . PHP_EOL;

echo S::TAB . S::TAB . S::TAB;
echo '<h1 class="result-message error-num color-text--error">' . $errorNum . '</h1>' . PHP_EOL;
echo S::TAB . S::TAB . S::TAB;
echo '<h1 class="result-message color-text--error">' . $errorHeader . '</h1>' . PHP_EOL;
echo S::TAB . S::TAB . S::TAB;
echo '<h2 class="result-message color-text--secondary">' . $errorSubHeader . '</h2>' . PHP_EOL;

if (strcasecmp($_SERVER['REQUEST_URI'], "/index.php")) {
    echo S::TAB . S::TAB . S::TAB;
    echo '<h2 class="result-message link"><a href="/index.php">' . S::ERROR_GOTO_START . '</a></h2>' . PHP_EOL;
}

echo S::TAB . S::TAB;
echo '</div>' . PHP_EOL;
echo S::TAB;
echo '</div>' . PHP_EOL;
echo '</div>' . PHP_EOL;

echoEndBody($newDesign);

echoEndPage();
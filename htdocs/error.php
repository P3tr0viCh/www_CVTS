<?php
require_once "include/Strings.php";
require_once "include/Functions.php";
require_once "include/ParamName.php";
require_once "include/CheckBrowser.php";

require_once "include/echo_html_page.php";

require_once "include/builders/href_builder/Builder.php";

use builders\href_builder\Builder;
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
    case 412:
        $errorHeader = S::ERROR_412_HEADER;
        $errorSubHeader = S::ERROR_412_SUB_HEADER;
        break;
    case 530:
        $errorHeader = S::ERROR_530_HEADER;
        $errorSubHeader = S::ERROR_530_SUB_HEADER;
        break;
    case 531:
        $errorHeader = S::ERROR_531_HEADER;
        $errorSubHeader = S::ERROR_531_SUB_HEADER;
        break;
    case 500:
    default:
        $errorHeader = S::ERROR_500_HEADER;
        $errorSubHeader = S::ERROR_500_SUB_HEADER;
}

echoStartPage();

$styles[] = "/styles/error_common.css";
if ($newDesign) {
    $styles[] = "/styles/error.css";
    $styles[] = "/fonts/roboto/roboto.css";
    $styles[] = '/fonts/robotottf/roboto.css';
} else {
    $styles[] = "/styles/error_compat.css";
}

echoHead($newDesign, S::TITLE_ERROR . " " . $errorNum, $styles, null, false);

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

echo S::TAB . S::TAB . S::TAB;

$href = Builder::getInstance()
    ->setUrl("index.php")
    ->setParam(getCookieAsBool(ParamName::NEW_DESIGN) ? ParamName::NEW_DESIGN : null, true)
    ->build();
$href = "http://" . $_SERVER['HTTP_HOST'] . "/" . $href;

echo "<h2 class='result-message link'><a href='$href'>" . S::ERROR_GOTO_START . "</a></h2>" . PHP_EOL;

echo S::TAB . S::TAB;
echo '</div>' . PHP_EOL;
echo S::TAB;
echo '</div>' . PHP_EOL;
echo '</div>' . PHP_EOL;

echoEndBody($newDesign);

echoEndPage();
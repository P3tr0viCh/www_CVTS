<?php
require_once "include/Constants.php";
require_once "include/Strings.php";

require_once "include/CheckBrowser.php";

require_once "include/echo_html_page.php";

use Strings as S;

$newDesign = false;

echoStartPage();

echoHead($newDesign, S::TITLE_ERROR_INCOMPATIBLE_BROWSER);

echoStartBody($newDesign);

echo '<div class="div-center-outer--center">' . PHP_EOL;
echo S::TAB;
echo '<div class="div-center-middle">' . PHP_EOL;
echo S::TAB . S::TAB;
echo '<div class="div-center-inner">' . PHP_EOL;

echo S::TAB . S::TAB . S::TAB;
echo '<h1 class="result-message color-text--error">' . S::ERROR_INCOMPATIBLE_BROWSER_HEADER . '</h1>' . PHP_EOL;
echo S::TAB . S::TAB . S::TAB;
echo '<div class="text-align--center">' . PHP_EOL;
echo S::TAB . S::TAB . S::TAB;
echo '<div class="result-message color-text--primary">' . PHP_EOL;
echo S::TAB . S::TAB . S::TAB;
echo '<p>' . PHP_EOL;
echo S::TAB . S::TAB . S::TAB . S::TAB;
echo '<span>' . S::TEXT_COMPATIBLE_BROWSERS_START . '</span>' . PHP_EOL;
echo S::TAB . S::TAB . S::TAB . S::TAB;
echo '<ul class="margin-top-bottom--default-2x">' . PHP_EOL;
echo S::TAB . S::TAB . S::TAB . S::TAB . S::TAB;
echo '<li class="margin-top-bottom--default-2x">' .
    sprintf(S::TEXT_COMPATIBLE_BROWSER_IE, CheckBrowser::COMPATIBLE_MIN_VERSION_IE) . '</li>' . PHP_EOL;
echo S::TAB . S::TAB . S::TAB . S::TAB . S::TAB;
echo '<li class="margin-top-bottom--default-2x">' .
    sprintf(S::TEXT_COMPATIBLE_BROWSER_EDGE, CheckBrowser::COMPATIBLE_MIN_VERSION_EDGE) . '</li>' . PHP_EOL;
echo S::TAB . S::TAB . S::TAB . S::TAB . S::TAB;
echo '<li class="margin-top-bottom--default-2x">' .
    sprintf(S::TEXT_COMPATIBLE_BROWSER_CHROME, CheckBrowser::COMPATIBLE_MIN_VERSION_CHROME) . '</li>' . PHP_EOL;
echo S::TAB . S::TAB . S::TAB . S::TAB;
echo '</ul>' . PHP_EOL;;
echo S::TAB . S::TAB . S::TAB;
echo '</p>' . PHP_EOL;
echo S::TAB . S::TAB . S::TAB;
echo '<br>' . PHP_EOL;
echo S::TAB . S::TAB . S::TAB;
echo '<p>' . PHP_EOL;
echo S::TAB . S::TAB . S::TAB . S::TAB;
echo '<span>' . S::TEXT_COMPATIBLE_BROWSERS_END . '</span>' . PHP_EOL;
echo S::TAB . S::TAB . S::TAB;
echo '</p>' . PHP_EOL;
echo S::TAB . S::TAB . S::TAB;
echo '</div>' . PHP_EOL;
echo S::TAB . S::TAB . S::TAB;
echo '</div>' . PHP_EOL;

echo S::TAB . S::TAB . S::TAB;
echo '<h2 class="result-message link"><a href="/index.php">' . S::ERROR_GOTO_START_COMPATIBLE . '</a></h2>' . PHP_EOL;

echo S::TAB . S::TAB;
echo '</div>' . PHP_EOL;
echo S::TAB;
echo '</div>' . PHP_EOL;
echo '</div>' . PHP_EOL;

echoEndBody($newDesign);

echoEndPage();
<?php
require_once "include/Constants.php";
require_once "include/Strings.php";
require_once "include/Functions.php";

require_once "include/CheckBrowser.php";

require_once "include/echo_html_page.php";

use Strings as S;

$newDesign = isNewDesign(CheckBrowser::isCompatibleVersion());

echoStartPage();

CheckBrowser::check($newDesign, false);

echoHead($newDesign, S::ERROR_403_TITLE);

echoStartBody($newDesign);

echo '<div class="div-center-outer--center">' . PHP_EOL;
echo S::TAB;
echo '<div class="div-center-middle">' . PHP_EOL;
echo S::TAB . S::TAB;
echo '<div class="div-center-inner">' . PHP_EOL;

echo S::TAB . S::TAB . S::TAB;
echo '<h1 class="result-message error-num color-text--error">' . S::ERROR_403 . '</h1>' . PHP_EOL;
echo S::TAB . S::TAB . S::TAB;
echo '<h1 class="result-message color-text--error">' . S::ERROR_403_HEADER . '</h1>' . PHP_EOL;
echo S::TAB . S::TAB . S::TAB;
echo '<h2 class="result-message color-text--secondary">' . S::ERROR_403_SUB_HEADER . '</h2>' . PHP_EOL;

echo S::TAB . S::TAB . S::TAB;
echo '<h2 class="result-message link"><a href="/index.php">' . S::ERROR_GOTO_START . '</a></h2>' . PHP_EOL;

echo S::TAB . S::TAB;
echo '</div>' . PHP_EOL;
echo S::TAB;
echo '</div>' . PHP_EOL;
echo '</div>' . PHP_EOL;

echoEndBody($newDesign);

echoEndPage();
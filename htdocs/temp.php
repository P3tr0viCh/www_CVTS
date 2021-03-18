<?php
require_once "include/Strings.php";

require_once "include/Functions.php";
require_once "include/CheckBrowser.php";
require_once "include/echo_html_page.php";

use Strings as S;

$newDesign = isNewDesign(true);

$debug = getParamGETAsBool(ParamName::DEBUG, false);
$nightMode = getParamGETAsBool(ParamName::NIGHT_MODE);

echoStartPage();

$styles = array();
$styles[] = '/styles/temp_common.css';
if ($newDesign) {
    $styles[] = '/styles/temp.css';
} else {
    $styles[] = '/styles/temp_compat.css';
}

if ($debug) {
    $styles[] = '/styles/temp_debug.css';
}

$javaScripts = array();
$javaScripts[] = '/javascript/class_utils.js';
$javaScripts[] = '/javascript/temp.js';
$javaScripts[] = '/javascript/night_mode.js';
$javaScripts[] = '/javascript/hide_cursor.js';

echoHead($newDesign, S::TITLE_TEMP, $styles, $javaScripts);

echoStartBody($newDesign, 'temp()');

echo "<div class='temp'>" . PHP_EOL;

echo S::TAB;
echo "<div class='temp--row'>" . PHP_EOL;
echo S::TAB . S::TAB;
echo "<div id='temp' class='temp--cell'></div>" . PHP_EOL;
echo S::TAB;
echo "</div>" . PHP_EOL;

echo "</div> <!-- class='temp' -->" . PHP_EOL;

echo "<div class='temp--copyright left-section show-on-hover'>" . PHP_EOL;
echo S::TAB;
echo S::FOOTER_LEFT_SECTION . PHP_EOL;
echo "</div>" . PHP_EOL;
echo "<div class='temp--copyright right-section show-on-hover'>" . PHP_EOL;
echo S::TAB;
echo S::FOOTER_RIGHT_SECTION . PHP_EOL;
echo "</div>" . PHP_EOL;

if ($nightMode !== null) {
    echo "<div class='set-night-mode show-on-hover'>" . PHP_EOL;
    echo S::TAB;
    echo "<a onclick='toggleNightMode()'>" . S::TEXT_NIGHT_MODE . "</a>" . PHP_EOL;
    echo "</div>" . PHP_EOL;
}

echoJSDisabled();

$javaScripts = array();
$javaScripts[] = 'initClassUtils();';
$javaScripts[] = 'startHideCursor();';
$javaScripts[] = 'nightMode(' . boolToString($nightMode) . ');';
echoEndBody($newDesign, $javaScripts);

echoEndPage();
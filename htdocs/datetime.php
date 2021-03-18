<?php
require_once "include/Strings.php";

require_once "include/Functions.php";
require_once "include/CheckBrowser.php";
require_once "include/echo_html_page.php";

use Strings as S;

$newDesign = isNewDesign(true);

$showSeconds = getParamGETAsBool(ParamName::SHOW_SECONDS, false);
$debug = getParamGETAsBool(ParamName::DEBUG, false);
$nightMode = getParamGETAsBool(ParamName::NIGHT_MODE);

echoStartPage();

$styles = array();
$styles[] = '/styles/datetime_common.css';
if ($newDesign) {
    $styles[] = '/styles/datetime.css';
} else {
    $styles[] = '/styles/datetime_compat.css';
}

if ($debug) {
    $styles[] = '/styles/datetime_debug.css';
}

$javaScripts = array();
$javaScripts[] = '/javascript/class_utils.js';
$javaScripts[] = '/javascript/datetime_format.js';
$javaScripts[] = '/javascript/datetime.js';
$javaScripts[] = '/javascript/night_mode.js';
$javaScripts[] = '/javascript/hide_cursor.js';

echoHead($newDesign, S::TITLE_DATETIME, $styles, $javaScripts);

echoStartBody($newDesign, 'dateTime(' . ($showSeconds ? 'true' : 'false') . ')');

echo "<div class='datetime'>" . PHP_EOL;

echo S::TAB;
echo "<div class='datetime--row date'>" . PHP_EOL;
echo S::TAB . S::TAB;
echo "<div id='date' class='datetime--cell date'></div>" . PHP_EOL;
echo S::TAB;
echo "</div>" . PHP_EOL;

$classShowSeconds = $showSeconds ? " showSeconds" : "";
echo S::TAB;
echo "<div class='datetime--row time'>" . PHP_EOL;
echo S::TAB . S::TAB;
echo "<div id='time' class='datetime--cell time$classShowSeconds'></div>" . PHP_EOL;
echo S::TAB;
echo "</div>" . PHP_EOL;

echo "</div> <!-- class='datetime' -->" . PHP_EOL;

echo "<div class='datetime--copyright left-section show-on-hover'>" . PHP_EOL;
echo S::TAB;
echo S::FOOTER_LEFT_SECTION . PHP_EOL;
echo "</div>" . PHP_EOL;
echo "<div class='datetime--copyright right-section show-on-hover'>" . PHP_EOL;
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
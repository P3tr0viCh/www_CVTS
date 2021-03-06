<?php
require_once "include/Links.php";
require_once "include/Strings.php";
require_once "include/MetaInfo.php";
require_once "include/Functions.php";
require_once "include/ParamName.php";
require_once "include/CheckBrowser.php";

require_once "include/echo_html_page.php";

require_once "include/HtmlHeader.php";
require_once "include/HtmlFooter.php";

use Strings as S;

$newDesign = isNewDesign(true);

echoStartPage();

$styles = array();
$styles[] = '/styles/a_common.css';
if ($newDesign) {
    $styles[] = '/styles/a.css';
} else {
    $styles[] = '/styles/a_compat.css';
}

echoHead($newDesign, S::TITLE_A, $styles, "/javascript/footer.js");

echoStartBody($newDesign);

(new HtmlHeader($newDesign))
    ->setMainPage(true)
    ->setHeader(S::HEADER_PAGE_MAIN)
    ->setSubHeader(S::HEADER_PAGE_A)
    ->setDrawerIcon("home")
    ->draw();

echoStartMain($newDesign);

echoStartContent();

echo '<ul>' . PHP_EOL;

echo S::TAB;
echo '<li>' . '<a href="/index.php?' . ParamName::NEW_DESIGN . '=true">' . S::A_GOTO_MAIN . '</a>' . '</li>' . PHP_EOL;
echo S::TAB;
echo '<li>' . '<a href="/index.php?' . ParamName::NEW_DESIGN . '=false">' . S::A_GOTO_MAIN_COMPATIBLE . '</a>' . '</li>' . PHP_EOL;
echo S::TAB;
echo '<li>' . '<a href="/index.php?' . ParamName::NEW_DESIGN . '=false&' . ParamName::SHOW_DISABLED . '=true">' . S::A_GOTO_MAIN_COMPATIBLE_WITH_DISABLED . '</a>' . '</li>' . PHP_EOL;
echo S::TAB;
echo '<li>' . '<a href="/schemes.php?' . ParamName::NEW_DESIGN . '=true" target="_blank">' . S::A_GOTO_SCHEMES . '</a>' . '</li>' . PHP_EOL;

echo S::TAB . '<br>' . PHP_EOL;

echo S::TAB;
echo '<li>' . '<a href="/wac.php">' . S::A_GOTO_WAC . '</a>' . '</li>' . PHP_EOL;
echo S::TAB;
echo '<li>' . '<a href="/datetime.php">' . S::A_GOTO_DATETIME . '</a>' . '</li>' . PHP_EOL;
echo S::TAB;
echo '<li>' . '<a href="/mysql_datetime.php">' . S::A_GOTO_MYSQL_DATETIME . '</a>' . '</li>' . PHP_EOL;
echo S::TAB;
echo '<li>' . '<a href="/temp.php">' . S::A_GOTO_TEMP . '</a>' . '</li>' . PHP_EOL;

echo S::TAB . '<br>' . PHP_EOL;

echo S::TAB;
echo '<li>' . '<a href="/help.php">' . S::A_GOTO_HELP . '</a>' . '</li>' . PHP_EOL;

echo '</ul>' . PHP_EOL;

echo '<br>' . PHP_EOL;

echo '<h2>' . S::A_TEXT_EXTERNAL_RESOURCES . '</h2>' . PHP_EOL;

echo '<ul>' . PHP_EOL;

echo S::TAB;
echo '<li>' . '<a href="' . Links::DATA_CENTER_ARM . '">' . S::A_GOTO_DATA_CENTER_ARM . '</a>' . '</li>' . PHP_EOL;
echo S::TAB;
echo '<li>' . '<a href="' . Links::CTA_AND_KIP_ASU_GAZ . '">' . S::A_GOTO_CTA_AND_KIP_ASU_GAZ . '</a>' . '</li>' . PHP_EOL;

echo '</ul>' . PHP_EOL;

echo '<br>' . PHP_EOL;

echo '<p class="versions">' . PHP_EOL;

echo '<span>' . sprintf(S::TEXT_SITE_VERSION, MetaInfo::VERSION, MetaInfo::CREATION) . '</span>' . PHP_EOL;

echo '<br><br>' . PHP_EOL;

$apache_version = explode(" ", $_SERVER["SERVER_SOFTWARE"]);
$apache_version = explode("/", $apache_version[0]);
$apache_version = $apache_version[1];

echo '<span>' . sprintf(S::TEXT_APACHE_VERSION, $apache_version) . '</span>' . PHP_EOL;

echo '<br>' . PHP_EOL;

echo '<span>' . sprintf(S::TEXT_PHP_VERSION, phpversion()) . '</span>' . PHP_EOL;

echoEndContent();

(new HtmlFooter($newDesign))->draw();

echoEndMain($newDesign);

echoEndBody($newDesign, "updateContentMinHeightOnEndBody();");

echoEndPage();
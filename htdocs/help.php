<?php
require_once "include/Strings.php";

require_once "include/Links.php";

require_once "include/Functions.php";
require_once "include/CheckBrowser.php";
require_once "include/MetaInfo.php";

require_once "include/builders/href_builder/Builder.php";

require_once "include/echo_html_page.php";
require_once "include/echo_header.php";
require_once "include/echo_table.php";
require_once "include/echo_footer.php";

use Strings as S;

$newDesign = isNewDesign();

echoStartPage();

$styles = array();
$styles[] = '/styles/help_common.css';
if ($newDesign) {
    $styles[] = '/styles/help_new.css';
    $styles[] = '/fonts/roboto/roboto.css';
} else {
    $styles[] = '/styles/help_compat.css';
}

echoHead($newDesign, S::TITLE_HELP, $styles, "/javascript/footer.js");

echoStartBody($newDesign);

echoHeader($newDesign, true, S::HEADER_PAGE_MAIN, S::HEADER_PAGE_HELP);

echoStartMain($newDesign);

echoStartContent();

echo '<h2>' . S::HELP_TEXT_PARAMS . '</h2>' . PHP_EOL;

echo '<p>' . S::HELP_TEXT_PARAMS_TEXT . '</p>' . PHP_EOL;
echo '<p>' . S::HELP_TEXT_PARAMS_LIST_HEADER . '</p>' . PHP_EOL;

if ($newDesign) {
    $tableClass = "mdl-data-table mdl-shadow--4dp";
    $tableCellClass = "mdl-data-table__cell--non-numeric";
} else {
    $tableClass = "text-align--left";
    $tableCellClass = "";
}

echoTableStart($tableClass);

echoTableHeadStart();
echoTableTRStart();
{
    echoTableTH(S::HELP_TEXT_PARAMS_LIST_HEADER_1, $tableCellClass);
    echoTableTH(S::HELP_TEXT_PARAMS_LIST_HEADER_2, $tableCellClass);
    echoTableTH(S::HELP_TEXT_PARAMS_LIST_HEADER_3, $tableCellClass);
}
echoTableTREnd();
echoTableHeadEnd();

echoTableBodyStart();

echoTableTRStart();
{
    echoTableTD(ParamName::NEW_DESIGN, $tableCellClass);
    echoTableTD(S::HELP_TEXT_PARAMS_VALUE_BOOL, $tableCellClass);
    echoTableTD(S::HELP_TEXT_PARAMS_NEW_DESIGN, $tableCellClass);
}
echoTableTREnd();
echoTableTRStart();
{
    echoTableTD(ParamName::SHOW_METROLOGY, $tableCellClass);
    echoTableTD(S::HELP_TEXT_PARAMS_VALUE_BOOL, $tableCellClass);
    echoTableTD(S::HELP_TEXT_PARAMS_SHOW_METROLOGY, $tableCellClass);
}
echoTableTREnd();
echoTableTRStart();
{
    echoTableTD(ParamName::SHOW_DISABLED, $tableCellClass);
    echoTableTD(S::HELP_TEXT_PARAMS_VALUE_BOOL, $tableCellClass);
    echoTableTD(S::HELP_TEXT_PARAMS_SHOW_DISABLED, $tableCellClass);
}
echoTableTREnd();

echoTableBodyEnd();
echoTableEnd();

if ($newDesign) {
    echo '<br>' . PHP_EOL;
}
echo '<p>' . "Пример:" . '</p>' . PHP_EOL;

$hrefBuilder = \HrefBuilder\Builder::getInstance();
$exampleUrl = $hrefBuilder->setUrl("index.php")
    ->setParam(ParamName::NEW_DESIGN, false)
    ->setParam(ParamName::SHOW_METROLOGY, true)
    ->build();

echo '<p>' . PHP_EOL;
echo "<a href='$exampleUrl' target='_blank_self'>" . "http://" . $_SERVER['HTTP_HOST'] . "/" . $exampleUrl . "</a>" . PHP_EOL;
echo '</p>' . PHP_EOL;

echoEndContent();

echoFooter($newDesign);

echoEndMain($newDesign);

echoEndBody($newDesign, "updateContentMinHeightOnEndBody();");

echoEndPage();
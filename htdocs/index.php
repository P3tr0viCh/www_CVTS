<?php
require_once "include/MySQLConnection.php";

require_once "include/QueryScales.php";

require_once "include/Strings.php";
require_once "include/Constants.php";
require_once "include/ParamName.php";
require_once "include/ColumnsStrings.php";

require_once "include/Functions.php";
require_once "include/CheckUser.php";
require_once "include/CheckBrowser.php";
require_once "include/ResultMessage.php";

require_once "include/builders/href_builder/Builder.php";

require_once "include/echo_html_page.php";
require_once "include/echo_table.php";

require_once "include/HtmlHeader.php";
require_once "include/HtmlDrawer.php";
require_once "include/HtmlFooter.php";

use builders\href_builder\Builder;
use database\Columns as DBC;
use Strings as S;
use Constants as C;
use ParamName as PN;
use ColumnsStrings as CS;

$newDesign = isNewDesign();

$showDisabled = getParamGETAsBool(PN::SHOW_DISABLED, false);
$showMetrology = getParamGETAsBool(PN::SHOW_METROLOGY, false);
$showAllOperators = getParamGETAsBool(PN::SHOW_ALL_OPERATORS, false);
$useBackup = getParamGETAsBool(PN::USE_BACKUP, false);

setCookieAsBool(PN::NEW_DESIGN, $newDesign);
setCookieAsBool(PN::SHOW_DISABLED, $showDisabled);
setCookieAsBool(PN::SHOW_METROLOGY, $showMetrology);
setCookieAsBool(PN::SHOW_ALL_OPERATORS, $showAllOperators);

deleteCookie(PN::DATETIME_START_DAY);
deleteCookie(PN::DATETIME_START_MONTH);
deleteCookie(PN::DATETIME_START_YEAR);
deleteCookie(PN::DATETIME_START_HOUR);
deleteCookie(PN::DATETIME_START_MINUTES);
deleteCookie(PN::DATETIME_END_DAY);
deleteCookie(PN::DATETIME_END_MONTH);
deleteCookie(PN::DATETIME_END_YEAR);
deleteCookie(PN::DATETIME_END_HOUR);
deleteCookie(PN::DATETIME_END_MINUTES);

deleteCookie(PN::VAN_NUMBER);
deleteCookie(PN::CARGO_TYPE);
deleteCookie(PN::INVOICE_NUM);
deleteCookie(PN::INVOICE_SUPPLIER);
deleteCookie(PN::INVOICE_RECIPIENT);
deleteCookie(PN::ONLY_CHARK);

deleteCookie(PN::ALL_FIELDS);
deleteCookie(PN::SHOW_CARGO_DATE);
deleteCookie(PN::SHOW_DELTAS);
deleteCookie(PN::SHOW_TOTAL_SUMS);
deleteCookie(PN::COMPARE_FORWARD);
deleteCookie(PN::COMPARE_BY_BRUTTO);
deleteCookie(PN::SCALES);

echoStartPage();

echoHead($newDesign, $useBackup ? S::TITLE_MAIN_BACKUP : S::TITLE_MAIN, null, "/javascript/footer.js");

echoStartBody($newDesign);

$resultMessage = null;

$mysqli = MySQLConnection::getInstance($useBackup);

(new HtmlHeader($newDesign))
    ->setMainPage(true)
    ->setHeader(S::HEADER_PAGE_MAIN)
    ->setUseBackup($useBackup)
    ->draw();

(new HtmlDrawer($newDesign, $mysqli))
    ->setStartPage(true)
    ->setUseBackup($useBackup)
    ->draw();

echoStartMain($newDesign);

echoStartContent();

if ($mysqli) {
    if (!$mysqli->connect_errno) {
        $query = (new QueryScales())
            ->setShowDisabled($showDisabled)
            ->setShowAllOperators($showAllOperators);

        if (C::DEBUG_SHOW_QUERY) {
            echo $query->getQuery() . PHP_EOL;
        }

        $result = $mysqli->query($query->getQuery());

        if ($result) {
            if ($newDesign) {
                $tableClass = "mdl-data-table mdl-shadow--4dp center";
            } else {
                $tableClass = "center";
            }

            echoTableStart($tableClass);

            echoTableHeadStart();
            echoTableTRStart();
            {
                echoTableTH(CS::SCALE_NUMBER);
                echoTableTH(CS::SCALE_TYPE_TEXT);
                echoTableTH(CS::SCALE_CLASS_STATIC);
                echoTableTH(CS::SCALE_CLASS_DYNAMIC);
                echoTableTH(CS::SCALE_PLACE, $newDesign ? "mdl-data-table__cell--add-padding" : null);

                $numColumns = 5;

                if ($showMetrology) {
                    echoTableTH(CS::SCALE_MIN_CAPACITY);
                    echoTableTH(CS::SCALE_MAX_CAPACITY);
                    echoTableTH(CS::SCALE_DISCRETENESS);

                    $numColumns += 3;
                }
            }
            echoTableTREnd();
            echoTableHeadEnd();

            echoTableBodyStart();

            $numColor = false;

            $hrefBuilder = Builder::getInstance();
            $hrefBuilder
                ->setUrl("query.php")
                ->setParam($newDesign ? PN::NEW_DESIGN : null, true)
                ->setParam($useBackup ? PN::USE_BACKUP : null, true);

            $columns = array();
            for ($i = 0; $i < $result->field_count; $i++) {
                $columns[] = $result->fetch_field_direct($i)->name;
            }

            while ($row = $result->fetch_array()) {
                // 1981 -- номер весов для отладки
                if ($row[DBC::SCALE_NUM] == 1981) continue;

                $rowColorClass = getRowColorClass($numColor);

                $href = $hrefBuilder
                    ->setParam(PN::SCALE_NUM, $row[DBC::SCALE_NUM])
                    ->build();

                echoTableTRStart($newDesign ? "rowclick $rowColorClass" : $rowColorClass,
                    $newDesign ? "location.href=\"$href\"" : null);

                for ($i = 0; $i < $result->field_count; $i++) {
                    if ($columns[$i] == DBC::SCALE_DISABLED) {
                        continue;
                    }

                    if (!$showMetrology &&
                        ($columns[$i] == DBC::SCALE_MIN_CAPACITY ||
                            $columns[$i] == DBC::SCALE_MAX_CAPACITY ||
                            $columns[$i] == DBC::SCALE_DISCRETENESS)) {
                        continue;
                    }

                    $field = latin1ToUtf8($row[$i]);

                    $class = null;
                    switch ($columns[$i]) {
                        case DBC::SCALE_TYPE_TEXT:
                            if ($newDesign) {
                                $class = "mdl-data-table__cell--non-numeric";
                            }
                            break;
                        case DBC::SCALE_PLACE:
                            if ($newDesign) {
                                $class = "mdl-data-table__cell--non-numeric mdl-data-table__cell--add-padding";
                            }
                            break;
                        default:
                            if (!$newDesign) {
                                $class = "text-align--center";
                            }
                    }

                    $field = formatFieldValue($columns[$i], $field, true);

                    echoTableTD($field, $class,
                        !$newDesign &&
                        ($columns[$i] == DBC::SCALE_NUM ||
                            $columns[$i] == DBC::SCALE_PLACE) ? $href : null);
                } // for

                echoTableTREnd();

                $numColor = !$numColor;
            } // while

            $result->free();

            $class = $newDesign ? "mdl-data-table__cell--non-numeric mdl-data-table__cell--add-padding" : null;

            function addRow(string $name, int $value, bool $showQuery)
            {
                global $numColor, $newDesign, $useBackup, $class, $numColumns;

                $rowColorClass = getRowColorClass($numColor);

                $hrefBuilder = Builder::getInstance();
                $hrefBuilder
                    ->setUrl("query.php")
                    ->setParam($newDesign ? PN::NEW_DESIGN : null, true)
                    ->setParam($useBackup ? PN::USE_BACKUP : null, true);

                $href = match ($showQuery) {
                    true =>
                    $hrefBuilder
                        ->setUrl("query.php")
                        ->setParam(PN::SCALE_NUM, $value)
                        ->build(),
                    false =>
                    $hrefBuilder
                        ->setUrl("result.php")
                        ->setParam(PN::RESULT_TYPE, $value)
                        ->build()
                };

                echoTableTRStart($newDesign ? "rowclick $rowColorClass" : $rowColorClass,
                    $newDesign ? "location.href=\"$href\"" : null);

                echoTableTD($name, $class, $newDesign ? null : $href, $numColumns);

                echoTableTREnd();

                $numColor = !$numColor;
            }

            addRow(S::SHOW_ALL_TRAIN_SCALES, C::SCALE_NUM_ALL_TRAIN_SCALES, true);

            if (CheckUser::isPowerUser()) {
                addRow(S::SHOW_SENSORS_INFO_RESULT, ResultType::SENSORS_INFO, false);
            }

            addRow(S::SHOW_VANLIST_QUERY, C::SCALE_NUM_REPORT_VANLIST, true);
            addRow(S::SHOW_IRON_QUERY, C::SCALE_NUM_REPORT_IRON, true);
            addRow(S::SHOW_IRON_CONTROL_QUERY, C::SCALE_NUM_REPORT_IRON_CONTROL, true);
            addRow(S::SHOW_SLAG_CONTROL_QUERY, C::SCALE_NUM_REPORT_SLAG_CONTROL, true);

// end
            echoTableBodyEnd();
            echoTableEnd();
        } else {
            $resultMessage = queryError($mysqli);
        }

        $mysqli->close();
    } else {
        $resultMessage = connectionError($mysqli);
    }
} else {
    $resultMessage = mysqlConnectionFileError();
}

if ($resultMessage) {
    echoErrorPage($resultMessage->getError(), $resultMessage->getErrorDetails());
}

echoEndContent();

(new HtmlFooter($newDesign))->draw();

echoEndMain($newDesign);

echoEndBody($newDesign, "updateContentMinHeightOnEndBody();");

echoEndPage();
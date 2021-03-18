<?php
require_once "include/MySQLConnection.php";

require_once "include/QueryScales.php";

require_once "include/Strings.php";
require_once "include/Constants.php";
require_once "include/ColumnsStrings.php";

require_once "include/Functions.php";
require_once "include/CheckBrowser.php";
require_once "include/ResultMessage.php";

require_once "include/builders/href_builder/Builder.php";

require_once "include/echo_html_page.php";
require_once "include/echo_table.php";

require_once "include/HtmlHeader.php";
require_once "include/HtmlDrawer.php";
require_once "include/HtmlFooter.php";

use HrefBuilder\Builder;
use database\Columns as DBC;
use Strings as S;
use Constants as C;
use ParamName as PN;
use ColumnsStrings as CS;

// debug
$showQuery = false;

$newDesign = isNewDesign();

$showDisabled = getParamGETAsBool(PN::SHOW_DISABLED, false);
$showMetrology = getParamGETAsBool(PN::SHOW_METROLOGY, false);
$useBackup = getParamGETAsBool(PN::USE_BACKUP, false);

setCookieAsBool(PN::NEW_DESIGN, $newDesign);
setCookieAsBool(PN::SHOW_DISABLED, $showDisabled);
setCookieAsBool(PN::SHOW_METROLOGY, $showMetrology);

setCookieAsString(PN::DATETIME_START_DAY, null);
setCookieAsString(PN::DATETIME_START_MONTH, null);
setCookieAsString(PN::DATETIME_START_YEAR, null);
setCookieAsString(PN::DATETIME_START_HOUR, null);
setCookieAsString(PN::DATETIME_START_MINUTES, null);
setCookieAsString(PN::DATETIME_END_DAY, null);
setCookieAsString(PN::DATETIME_END_MONTH, null);
setCookieAsString(PN::DATETIME_END_YEAR, null);
setCookieAsString(PN::DATETIME_END_HOUR, null);
setCookieAsString(PN::DATETIME_END_MINUTES, null);

setCookieAsString(PN::VAN_NUMBER, null);
setCookieAsString(PN::CARGO_TYPE, null);
setCookieAsString(PN::INVOICE_NUM, null);
setCookieAsString(PN::INVOICE_SUPPLIER, null);
setCookieAsString(PN::INVOICE_RECIPIENT, null);
setCookieAsString(PN::ONLY_CHARK, null);

setCookieAsString(PN::ALL_FIELDS, null);
setCookieAsString(PN::SHOW_CARGO_DATE, null);
setCookieAsString(PN::SHOW_DELTAS, null);
setCookieAsString(PN::COMPARE_FORWARD, null);
setCookieAsString(PN::COMPARE_BY_BRUTTO, null);
setCookieAsString(PN::SCALES, null);

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
        $query = new QueryScales();

        if ($showQuery) {
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
                echoTableTH(CS::SCALE_NAME, $newDesign ? "mdl-data-table__cell--add-padding" : null);

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

                if ($row[DBC::SCALE_DISABLED] && !$showDisabled) continue;

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
                        case DBC::SCALE_CLASS_STATIC:
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

            /**
             * @param string $name
             * @param int $value
             */
            function addRowQuery(string $name, int $value)
            {
                global $numColor, $hrefBuilder, $newDesign, $class, $numColumns;

                $rowColorClass = getRowColorClass($numColor);

                $href = $hrefBuilder
                    ->setParam(PN::SCALE_NUM, $value)
                    ->build();

                echoTableTRStart($newDesign ? "rowclick $rowColorClass" : $rowColorClass,
                    $newDesign ? "location.href=\"$href\"" : null);

                echoTableTD($name, $class, $newDesign ? null : $href, $numColumns);

                echoTableTREnd();

                $numColor = !$numColor;
            }

            addRowQuery(S::SHOW_ALL_TRAIN_SCALES, C::SCALE_NUM_ALL_TRAIN_SCALES);

            addRowQuery(S::SHOW_VANLIST_QUERY, C::SCALE_NUM_REPORT_VANLIST);

            addRowQuery(S::SHOW_IRON_QUERY, C::SCALE_NUM_REPORT_IRON);

            addRowQuery(S::SHOW_IRON_CONTROL_QUERY, C::SCALE_NUM_REPORT_IRON_CONTROL);

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
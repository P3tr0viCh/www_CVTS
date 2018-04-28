<?php
require_once "include/MySQLConnection.php";

require_once "include/QueryScales.php";

require_once "include/Strings.php";
require_once "include/Constants.php";
require_once "include/ColumnsStrings.php";

require_once "include/Database.php";

require_once "include/Functions.php";
require_once "include/CheckBrowser.php";
require_once "include/ResultMessage.php";

require_once "include/builders/href_builder/Builder.php";

require_once "include/echo_html_page.php";
require_once "include/echo_table.php";

require_once "include/HtmlHeader.php";
require_once "include/HtmlDrawer.php";
require_once "include/HtmlFooter.php";

use Strings as S;
use ColumnsStrings as C;
use Database\Columns as DBC;

// debug
$showQuery = false;

$newDesign = isNewDesign();

$showDisabled = getParamGETAsBool(ParamName::SHOW_DISABLED, false);
$showMetrology = getParamGETAsBool(ParamName::SHOW_METROLOGY, false);
$useBackup = getParamGETAsBool(ParamName::USE_BACKUP, false);

setCookieAsBool(ParamName::NEW_DESIGN, $newDesign);
setCookieAsBool(ParamName::SHOW_DISABLED, $showDisabled);
setCookieAsBool(ParamName::SHOW_METROLOGY, $showMetrology);

setCookieAsString(ParamName::DATETIME_START_DAY, null);
setCookieAsString(ParamName::DATETIME_START_MONTH, null);
setCookieAsString(ParamName::DATETIME_START_YEAR, null);
setCookieAsString(ParamName::DATETIME_START_HOUR, null);
setCookieAsString(ParamName::DATETIME_START_MINUTES, null);
setCookieAsString(ParamName::DATETIME_END_DAY, null);
setCookieAsString(ParamName::DATETIME_END_MONTH, null);
setCookieAsString(ParamName::DATETIME_END_YEAR, null);
setCookieAsString(ParamName::DATETIME_END_HOUR, null);
setCookieAsString(ParamName::DATETIME_END_MINUTES, null);

setCookieAsString(ParamName::VAN_NUMBER, null);
setCookieAsString(ParamName::CARGO_TYPE, null);
setCookieAsString(ParamName::INVOICE_NUM, null);
setCookieAsString(ParamName::INVOICE_SUPPLIER, null);
setCookieAsString(ParamName::INVOICE_RECIPIENT, null);
setCookieAsString(ParamName::ONLY_CHARK, null);

setCookieAsString(ParamName::ALL_FIELDS, null);
setCookieAsString(ParamName::SHOW_CARGO_DATE, null);
setCookieAsString(ParamName::SHOW_DELTAS, null);
setCookieAsString(ParamName::COMPARE_FORWARD, null);
setCookieAsString(ParamName::COMPARE_BY_BRUTTO, null);
setCookieAsString(ParamName::SCALES, null);

echoStartPage();

echoHead($newDesign, $useBackup ? S::TITLE_MAIN_BACKUP : S::TITLE_MAIN, null, "/javascript/footer.js");

echoStartBody($newDesign);

$resultMessage = null;

$mysqli = MySQLConnection::getInstance($useBackup);

(new HtmlHeader($newDesign))
    ->setMainPage(true)
    ->setHeader(S::HEADER_PAGE_MAIN)
    ->setSubHeader($useBackup ? S::HEADER_PAGE_MAIN_BACKUP : null)
    ->setSubHeaderAddClass($useBackup ? "color-text--error" : null)
    ->draw();

(new HtmlDrawer($newDesign, $mysqli))
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
                echoTableTH(C::SCALE_NUMBER);
                echoTableTH(C::SCALE_TYPE_TEXT);
                echoTableTH(C::SCALE_CLASS_STATIC);
                echoTableTH(C::SCALE_CLASS_DYNAMIC);
                echoTableTH(C::SCALE_NAME, $newDesign ? "mdl-data-table__cell--add-padding" : null);

                $numColumns = 5;

                if ($showMetrology) {
                    echoTableTH(C::SCALE_MIN_CAPACITY);
                    echoTableTH(C::SCALE_MAX_CAPACITY);
                    echoTableTH(C::SCALE_DISCRETENESS);

                    $numColumns += 3;
                }
            }
            echoTableTREnd();
            echoTableHeadEnd();

            echoTableBodyStart();

            $numColor = false;

            $hrefBuilder = \HrefBuilder\Builder::getInstance();
            $hrefBuilder
                ->setUrl("query.php")
                ->setParam($newDesign ? ParamName::NEW_DESIGN : null, true)
                ->setParam($useBackup ? ParamName::USE_BACKUP : null, true);

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
                    ->setParam(ParamName::SCALE_NUM, $row[DBC::SCALE_NUM])
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

            $rowColorClass = getRowColorClass($numColor);

            $href = $hrefBuilder
                ->setParam(ParamName::SCALE_NUM, Constants::SCALE_NUM_ALL_TRAIN_SCALES)
                ->build();

            echoTableTRStart($newDesign ? "rowclick $rowColorClass" : $rowColorClass,
                $newDesign ? "location.href=\"$href\"" : null);

            $field = S::ALL_TRAIN_SCALES;

            echoTableTD($field, $newDesign ? "mdl-data-table__cell--non-numeric" : null,
                $newDesign ? null : $href, $numColumns);

            echoTableTREnd();

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
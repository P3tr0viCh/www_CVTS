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
require_once "include/echo_header.php";
require_once "include/echo_drawer.php";
require_once "include/echo_table.php";
require_once "include/echo_footer.php";

use Strings as S;
use ColumnsStrings as C;
use Database\Columns as DBC;

$newDesign = isNewDesign();

$showDisabled = getParamGETAsBool(ParamName::SHOW_DISABLED, false);
$showMetrology =  getParamGETAsBool(ParamName::SHOW_METROLOGY, false);;

setcookie(ParamName::SHOW_DISABLED, boolToString($showDisabled));
$_COOKIE[ParamName::SHOW_DISABLED] = boolToString($showDisabled);

echoStartPage();

echoHead($newDesign, S::TITLE_MAIN, null, "/javascript/footer.js");

echoStartBody($newDesign);

$resultMessage = null;

$mysqli = MySQLConnection::getInstance();

echoHeader($newDesign, true, S::HEADER_PAGE_MAIN);

echoDrawer($newDesign, $mysqli);

echoStartMain($newDesign);

echoStartContent();

if ($mysqli) {
    if (!$mysqli->connect_errno) {
        $query = new QueryScales();

//        echo $query->getQuery() . PHP_EOL;

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
                echoTableTH(C::SCALE_TYPE);
                echoTableTH(C::SCALE_CLASS_STATIC);
                echoTableTH(C::SCALE_CLASS_DYNAMIC);
                echoTableTH(C::SCALE_NAME, $newDesign ? "mdl-data-table__cell--add-padding" : null);

                if ($showMetrology) {
                    echoTableTH(C::SCALE_MIN_CAPACITY);
                    echoTableTH(C::SCALE_MIN_CAPACITY_35P);
                    echoTableTH(C::SCALE_MAX_CAPACITY);
                    echoTableTH(C::SCALE_MI_DELTA_MIN);
                    echoTableTH(C::SCALE_DISCRETENESS);
                }
            }
            echoTableTREnd();
            echoTableHeadEnd();

            echoTableBodyStart();

            $numColor = false;

            $hrefBuilder = \HrefBuilder\Builder::getInstance();

            $columns = array();
            for ($i = 0; $i < $result->field_count; $i++) {
                $columns[] = $result->fetch_field_direct($i)->name;
            }

            while ($row = $result->fetch_array()) {
                // 1981 -- номер весов для отладки
                if ($row[DBC::SCALE_NUM] == 1981) continue;

                if ($row[DBC::SCALE_DISABLED] && !$showDisabled) continue;

//                if ($row[DBC::SCALE_NUM] > 50) continue;

                $rowColorClass = getRowColorClass($numColor);

                $href = $hrefBuilder->setUrl("query.php")
                    ->setParam(ParamName::SCALE_NUM, $row[DBC::SCALE_NUM])
                    ->setParam(ParamName::NEW_DESIGN, $newDesign)
                    ->build();

                echoTableTRStart($newDesign ? "rowclick $rowColorClass" : $rowColorClass,
                    $newDesign ? "location.href=\"$href\"" : null);

                $numColumns = 0;

                for ($i = 0; $i < $result->field_count; $i++) {
                    if ($columns[$i] == DBC::SCALE_DISABLED) {
                        continue;
                    }

                    if (!$showMetrology &&
                        ($columns[$i] == DBC::SCALE_MIN_CAPACITY ||
                            $columns[$i] == DBC::SCALE_MIN_CAPACITY_35P ||
                            $columns[$i] == DBC::SCALE_MAX_CAPACITY ||
                            $columns[$i] == DBC::SCALE_MI_DELTA_MIN ||
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

                    $numColumns++;
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

echoFooter($newDesign);

echoEndMain($newDesign);

echoEndBody($newDesign, "updateContentMinHeightOnEndBody();");

echoEndPage();
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

$newDesign = isNewDesign();

echoStartPage();

CheckBrowser::check($newDesign, true);

echoHead($newDesign, S::MAIN_TITLE, null, "/javascript/footer.js");

echoStartBody($newDesign);

$resultMessage = null;

$mysqli = MySQLConnection::getInstance();

echoHeader($newDesign, true, S::MAIN_HEADER);

echoDrawer($newDesign, $mysqli);

echoStartMain($newDesign);

echoStartContent();

if ($mysqli) {
    if (!$mysqli->connect_errno) {
        $query = new QueryScales();
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
                echoTableTH(ColumnsStrings::SCALE_NUMBER);
                echoTableTH(ColumnsStrings::SCALE_TYPE);
                echoTableTH(ColumnsStrings::SCALE_CLASS_STATIC);
                echoTableTH(ColumnsStrings::SCALE_CLASS_DYNAMIC);
                echoTableTH(ColumnsStrings::SCALE_NAME, $newDesign ? "mdl-data-table__cell--add-padding" : null);
            }
            echoTableTREnd();
            echoTableHeadEnd();

            echoTableBodyStart();

            $numColor = false;

            $hrefBuilder = \HrefBuilder\Builder::getInstance();

            while ($row = $result->fetch_array()) {
                // 1981 -- номер весов для отладки
                if ($row[Database\Columns::SCALE_NUM] == 1981) continue;

//            if ($row[Database\Columns::SCALE_NUM] > 50) continue;

                $rowColorClass = getRowColorClass($numColor);

                $href = $hrefBuilder->setUrl("query.php")
                    ->setParam(ParamName::SCALE_NUM, $row[Database\Columns::SCALE_NUM])
                    ->setParam(ParamName::NEW_DESIGN, $newDesign)
                    ->build();

                echoTableTRStart($newDesign ? "rowclick $rowColorClass" : $rowColorClass,
                    $newDesign ? "location.href=\"$href\"" : null);

                for ($i = 0; $i < $result->field_count; $i++) {
                    $field = $row[$i];
                    $column = $result->fetch_field_direct($i)->name;

                    $class = null;
                    switch ($column) {
                        case Database\Columns::SCALE_NUM:
                        case Database\Columns::SCALE_CLASS_DYNAMIC:
                            if (!$newDesign) {
                                $class = "text-align--center";
                            }
                            break;
                        case Database\Columns::SCALE_PLACE:
                            if ($newDesign) {
                                $class = "mdl-data-table__cell--non-numeric mdl-data-table__cell--add-padding";
                            }
                            break;
                        default:
                            if ($newDesign) {
                                $class = "mdl-data-table__cell--non-numeric";
                            }
                    }

                    $field = formatFieldValue($column, $field, true);

                    echoTableTD($field, $class,
                        !$newDesign &&
                        ($column == Database\Columns::SCALE_NUM ||
                            $column == Database\Columns::SCALE_PLACE) ? $href : null);
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
                $newDesign ? null : $href, "5");

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
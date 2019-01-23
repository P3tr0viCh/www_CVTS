<?php
/**
 * wac - without accident counters
 */

require_once "include/MySQLConnection.php";

require_once "include/QueryWAC.php";

require_once "include/Strings.php";

require_once "include/Functions.php";
require_once "include/CheckBrowser.php";
require_once "include/ResultMessage.php";

require_once "include/echo_html_page.php";
require_once "include/echo_table.php";

use Strings as S;

// debug
$showQuery = false;

$newDesign = isNewDesign(true);
$useBackup = getParamGETAsBool(ParamName::USE_BACKUP, false);

$debug = getParamGETAsBool(ParamName::DEBUG, false);
$disableHideCursor = getParamGETAsBool(ParamName::DISABLE_HIDE_CURSOR);
$nightMode = getParamGETAsBool(ParamName::NIGHT_MODE);
$department = getParamGETAsInt(ParamName::DEPARTMENT, 0);
$dateFormat = getParamGETAsInt(ParamName::DATE_FORMAT, 0);;

$companyDate = null;
$departmentDate = null;
$departmentName = null;

echoStartPage();

$oldIEStyle = null;

$styles = array();
$styles[] = '/styles/wac_common.css';
if ($newDesign) {
    $styles[] = '/styles/wac.css';
} else {
    $styles[] = '/styles/wac_compat.css';
    $oldIEStyle = '/styles/wac_compat_ie.css';
}

if ($debug) {
    $styles[] = '/styles/wac_debug.css';
}

$javaScripts = array();
$javaScripts[] = '/javascript/class_utils.js';
$javaScripts[] = '/javascript/datetime_format.js';
$javaScripts[] = '/javascript/wac.js';
$javaScripts[] = '/javascript/night_mode.js';

if (!$disableHideCursor) {
    $javaScripts[] = '/javascript/hide_cursor.js';
}

echoHead($newDesign, S::TITLE_WAC, $styles, $javaScripts, $oldIEStyle);

echoStartBody($newDesign);

$resultMessage = null;

$mysqli = MySQLConnection::getInstance($useBackup, \Database\Info::CVTS);

if ($mysqli) {
    if (!$mysqli->connect_errno) {
        $query = new QueryWAC();

        $query->setDepartment($department);

        if ($showQuery) {
            echo $query->getQuery() . PHP_EOL;
        }

        $result = $mysqli->query($query->getQuery());

        if ($result) {
            $row = $result->fetch_array();

            $companyDate = $row[Database\Columns::COMPANY_DATE];
            $departmentDate = $row[Database\Columns::DEPARTMENT_DATE];
            $departmentName = latin1ToUtf8($row[Database\Columns::DEPARTMENT_NAME]);

            if (is_null($departmentName)) {
                $departmentName = sprintf(S::HEADER_WAC_DEPARTMENT, $department);
            }

            echoTableStart("wac");

            echoTableHeadStart();
            echoTableTRStart("wac--row header");
            echoTableTH(
                "<div id='date' class='wac--header-text left'></div>" .
                "<div id='time' class='wac--header-text right'></div>", "wac--cell header datetime", 2);
            echoTableTREnd();
            echoTableTRStart("wac--row header");
            echoTableTH("<div class='hr'></div>", "wac--cell header", 2);
            echoTableTREnd();
            echoTableTRStart("wac--row header");
            echoTableTH(S::HEADER_WAC, "wac--cell header text", 2);
            echoTableTREnd();
            echoTableHeadEnd();

            echoTableBodyStart();
            echoTableTRStart("wac--row data");
            echoTableTD($departmentName, "wac--cell department name");
            echoTableTD("<span id='department'></span>", "wac--cell department counter");
            echoTableTREnd();

            echoTableTRStart("wac--row data");
            echoTableTD(S::HEADER_WAC_COMPANY, "wac--cell company name");
            echoTableTD("<span id='company'></span>", "wac--cell company counter");
            echoTableBodyEnd();

            echoTableEnd();

            echo "<div class='wac--copyright left-section show-on-hover'>" . PHP_EOL;
            echo S::TAB;
            echo S::FOOTER_LEFT_SECTION . PHP_EOL;
            echo "</div>" . PHP_EOL;
            echo "<div class='wac--copyright right-section show-on-hover'>" . PHP_EOL;
            echo S::TAB;
            echo S::FOOTER_RIGHT_SECTION . PHP_EOL;
            echo "</div>" . PHP_EOL;

            if ($nightMode !== null) {
                echo "<div class='set-night-mode show-on-hover'>" . PHP_EOL;
                echo S::TAB;
                echo "<a onclick='toggleNightMode()'>" . S::TEXT_NIGHT_MODE . "</a>" . PHP_EOL;
                echo "</div>" . PHP_EOL;
            }
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

if (!$resultMessage) {
    $javaScripts = array();
    $javaScripts[] = 'initClassUtils();';

    if (!$disableHideCursor) {
        $javaScripts[] = "startHideCursor();";
    }

    $javaScripts[] = "startWAC('$companyDate', '$departmentDate', $dateFormat);";
    $javaScripts[] = "nightMode(" . boolToString($nightMode) . ");";
} else {
    $javaScripts = null;
}

echoJSDisabled();

echoEndBody($newDesign, $javaScripts);

echoEndPage();
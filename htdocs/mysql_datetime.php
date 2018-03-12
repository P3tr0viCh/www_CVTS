<?php
require_once "include/MySQLConnection.php";

require_once "include/QueryDateTime.php";

require_once "include/Constants.php";
require_once "include/Strings.php";

require_once "include/Database.php";

require_once "include/Functions.php";
require_once "include/CheckBrowser.php";
require_once "include/ResultMessage.php";

require_once "include/echo_html_page.php";

use Strings as S;

$newDesign = isNewDesign(true);
$useBackup = getParamGETAsBool(ParamName::USE_BACKUP, false);

echoStartPage();

echoHead($newDesign, S::TITLE_MYSQL_DATETIME, '/styles/mysql_datetime.css');

echoStartBody($newDesign);

echo '<div class="div-center-outer--center">' . PHP_EOL;
echo S::TAB;
echo '<div class="div-center-middle">' . PHP_EOL;
echo S::TAB . S::TAB;
echo '<div class="div-center-inner">' . PHP_EOL;

echo S::TAB . S::TAB . S::TAB;
echo '<h1 class="mysql_datetime color-text--primary">' . S::HEADER_PAGE_MYSQL_DATETIME . '</h1>' . PHP_EOL;

$resultMessage = null;

$mysqli = MySQLConnection::getInstance($useBackup);

if ($mysqli) {
    if ($mysqli->connect_errno) {
        $resultMessage = connectionError($mysqli);
    } else {
        $query = new QueryDateTime();
        $result = $mysqli->query($query->getQuery());

        if ($result) {
            $row = $result->fetch_array();

            $datetime = $row[Database\Columns::DATETIME_NOW];

            echo S::TAB . S::TAB . S::TAB;
            echo "<h2 class='mysql_datetime color-text--primary'>$datetime</h2>" . PHP_EOL;
        } else {
            $resultMessage = queryError($mysqli);
        }
    }
} else {
    $resultMessage = mysqlConnectionFileError();
}

if ($resultMessage) {
    echo S::TAB . S::TAB . S::TAB;
    echo '<h1 class="result-message color-text--error">' . $resultMessage->getError() . '</h1>' . PHP_EOL;

    if ($resultMessage->getErrorDetails()) {
        echo S::TAB . S::TAB . S::TAB;
        echo '<h2 class="result-message color-text--secondary">' . $resultMessage->getErrorDetails() . '</h2>' . PHP_EOL;
    }
}

echo S::TAB . S::TAB;
echo '</div>' . PHP_EOL;
echo S::TAB;
echo '</div>' . PHP_EOL;
echo '</div>' . PHP_EOL;

echoEndBody($newDesign);

echoEndPage();
<?php
require_once "Strings.php";
require_once "Constants.php";
require_once "Database.php";
require_once "Functions.php";
require_once "builders/href_builder/Builder.php";

require_once "QueryDrawer.php";

use Strings as S;

/**
 * @param bool $newDesign
 * @param null|mysqli $mysqli
 */
function echoDrawer($newDesign, $mysqli)
{
    if (!$newDesign || $mysqli == null || $mysqli->connect_error) return;

    $query = new QueryDrawer();
    if ($result = @$mysqli->query($query->getQuery())) {
        echo '<div class="mdl-layout__drawer">' . PHP_EOL;
        echo S::TAB;
        echo '<span class="mdl-layout-title">' . S::DRAWER_TITLE . '</span>' . PHP_EOL;
        echo S::TAB;
        echo '<nav class="mdl-navigation">' . PHP_EOL;

        $hrefBuilder = \HrefBuilder\Builder::getInstance();

        $href = $hrefBuilder->setUrl("index.php")
            ->setParam(ParamName::NEW_DESIGN, true)
            ->build();

        echo S::TAB . S::TAB;
        /** @noinspection JSUnusedGlobalSymbols */
        echo "<a class='mdl-navigation__link' href='//' onclick=\"this.href='$href'\">";
        echo S::DRAWER_START_PAGE;
        echo '</a>' . PHP_EOL;

        $hrefBuilder->setUrl("query.php");

        while ($row = $result->fetch_array()) {
            if ($row[Database\Columns::SCALE_NUM] == 1981) continue;

            $href = $hrefBuilder
                ->setParam(ParamName::SCALE_NUM, $row[Database\Columns::SCALE_NUM])
                ->build();

            echo S::TAB . S::TAB;
            /** @noinspection JSUnusedGlobalSymbols */
            echo "<a class='mdl-navigation__link' href='//' onclick=\"this.href='$href'\">";
            echo latin1ToUtf8($row[Database\Columns::SCALE_PLACE]);
            echo '</a>' . PHP_EOL;
        }

        $result->free();

        $href = $hrefBuilder
            ->setParam(ParamName::SCALE_NUM, Constants::SCALE_NUM_ALL_TRAIN_SCALES)
            ->build();

        echo S::TAB . S::TAB;
        /** @noinspection JSUnusedGlobalSymbols */
        echo "<a class='mdl-navigation__link' href='//' onclick=\"this.href='$href'\">";
        echo S::DRAWER_ALL_TRAIN_SCALES;
        echo '</a>' . PHP_EOL;

        echo S::TAB;
        echo '</nav>' . PHP_EOL;
        echo '</div> <!-- class="mdl-layout__drawer" -->' . PHP_EOL . PHP_EOL;
    }
}
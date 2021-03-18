<?php
require_once "Strings.php";
require_once "Constants.php";
require_once "Functions.php";
require_once "builders/href_builder/Builder.php";

require_once "QueryDrawer.php";

require_once "HtmlBase.php";

use HrefBuilder\Builder;
use JetBrains\PhpStorm\Pure;
use Strings as S;

class HtmlDrawer extends HtmlBase
{
    private ?mysqli $mysqli;

    private bool $isStartPage;
    private bool $useBackup;

    #[Pure] public function __construct(bool $newDesign, ?mysqli $mysqli)
    {
        parent::__construct($newDesign);

        $this->mysqli = $mysqli;

        $this->isStartPage = false;
        $this->useBackup = false;
    }

    public function setStartPage(bool $isStartPage): static
    {
        $this->isStartPage = $isStartPage;
        return $this;
    }

    public function setUseBackup(bool $useBackup): static
    {
        $this->useBackup = $useBackup;
        return $this;
    }

    protected function drawNewDesign()
    {
        if (is_null($this->mysqli) || $this->mysqli->connect_error) return;

        $showDisabled = getCookieAsBool(ParamName::SHOW_DISABLED);
        $showMetrology = getCookieAsBool(ParamName::SHOW_METROLOGY);

        $query = new QueryDrawer();

//        echo $query->getQuery() . PHP_EOL;

        if ($result = @$this->mysqli->query($query->getQuery())) {
            echo '<div class="mdl-layout__drawer">' . PHP_EOL;
            echo S::TAB;
            echo '<span class="mdl-layout-title">' . S::DRAWER_TITLE . '</span>' . PHP_EOL;
            echo S::TAB;
            echo '<nav class="mdl-navigation">' . PHP_EOL;

            $hrefBuilder = Builder::getInstance();

            $href = $hrefBuilder->setUrl("index.php")
                ->setParam(ParamName::NEW_DESIGN, true)
                ->setParam($showDisabled ? ParamName::SHOW_DISABLED : null, true)
                ->setParam($showMetrology ? ParamName::SHOW_METROLOGY : null, true)
                ->setParam($this->useBackup ? ParamName::USE_BACKUP : null, true)
                ->build();

            echo S::TAB . S::TAB;
            /** @noinspection JSUnusedGlobalSymbols */
            echo "<a class='mdl-navigation__link' href='//' onclick=\"this.href='$href'\">";
            echo S::DRAWER_START_PAGE;
            echo '</a>' . PHP_EOL;

            $hrefBuilder
                ->clear()
                ->setUrl("query.php")
                ->setParam(ParamName::NEW_DESIGN, true)
                ->setParam($this->useBackup ? ParamName::USE_BACKUP : null, true);

            function echoLink($name, $href)
            {
                echo S::TAB . S::TAB;
                echo "<a class='mdl-navigation__link' href='//' onclick=\"this.href='$href'\">";
                echo $name;
                echo '</a>' . PHP_EOL;
            }

            while ($row = $result->fetch_array()) {
                if ($row[database\Columns::SCALE_NUM] == 1981) continue;

                if ($row[database\Columns::SCALE_DISABLED] && !$showDisabled) continue;

                $href = $hrefBuilder
                    ->setParam(ParamName::SCALE_NUM, $row[database\Columns::SCALE_NUM])
                    ->build();

                echoLink(latin1ToUtf8($row[database\Columns::SCALE_PLACE]), $href);
            }

            $result->free();

            $href = $hrefBuilder
                ->setParam(ParamName::SCALE_NUM, Constants::SCALE_NUM_ALL_TRAIN_SCALES)
                ->build();
            echoLink(S::DRAWER_SHOW_ALL_TRAIN_SCALES, $href);

            $href = $hrefBuilder
                ->setParam(ParamName::SCALE_NUM, Constants::SCALE_NUM_REPORT_VANLIST)
                ->build();
            echoLink(S::DRAWER_SHOW_VANLIST_QUERY, $href);

            $href = $hrefBuilder
                ->setParam(ParamName::SCALE_NUM, Constants::SCALE_NUM_REPORT_IRON)
                ->build();
            echoLink(S::DRAWER_SHOW_IRON_QUERY, $href);

            $href = $hrefBuilder
                ->setParam(ParamName::SCALE_NUM, Constants::SCALE_NUM_REPORT_IRON_CONTROL)
                ->build();
            echoLink(S::DRAWER_SHOW_IRON_CONTROL_QUERY, $href);

            // Карта сайта
            $href = $hrefBuilder
                ->clear()
                ->setUrl("a.php")
                ->setParam(ParamName::NEW_DESIGN, true)
                ->build();
            echoLink(S::DRAWER_A, $href);

            // Настройки главного экрана
            if ($this->isStartPage) {
                echo '</nav>' . PHP_EOL;
                echo '<span class="mdl-layout-title mdl-layout-title__subtitle nav">' . S::DRAWER_TITLE_SETTINGS . '</span>' . PHP_EOL;
                echo '<nav class="mdl-navigation">' . PHP_EOL;

                $hrefBuilder
                    ->clear()
                    ->setUrl("index.php")
                    ->setParam(ParamName::NEW_DESIGN, true);

                $hrefBuilderShowDisabled = clone $hrefBuilder;
                $href = $hrefBuilderShowDisabled
                    ->setParam(!$showDisabled ? ParamName::SHOW_DISABLED : null, true)
                    ->setParam($showMetrology ? ParamName::SHOW_METROLOGY : null, true)
                    ->build();

                echoLink($showDisabled ? S::DRAWER_SHOW_DISABLED_OFF : S::DRAWER_SHOW_DISABLED_ON, $href);

                $hrefBuilderShowMetrology = clone $hrefBuilder;
                $href = $hrefBuilderShowMetrology
                    ->setParam($showDisabled ? ParamName::SHOW_DISABLED : null, true)
                    ->setParam(!$showMetrology ? ParamName::SHOW_METROLOGY : null, true)
                    ->build();

                echoLink($showMetrology ? S::DRAWER_SHOW_METROLOGY_OFF : S::DRAWER_SHOW_METROLOGY_ON, $href);
            }

            echo S::TAB;
            echo '</nav>' . PHP_EOL;
            echo '</div> <!-- class="mdl-layout__drawer" -->' . PHP_EOL . PHP_EOL;
        }
    }

    protected function drawCompat()
    {
    }
}
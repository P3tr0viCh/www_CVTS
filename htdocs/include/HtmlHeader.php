<?php
require_once "Strings.php";

require_once "HtmlBase.php";

require_once "HtmlHeaderNavLink.php";
require_once "HtmlHeaderMenuItem.php";

use Strings as S;

class HtmlHeader extends HtmlBase
{
    private bool $mainPage = true;

    private string $header = "";
    private ?string $subHeader = null;

    private bool $useBackup = false;

    private ?string $drawerIcon = null;

    private ?array $navLinks = null;
    private ?array $menuItems = null;

    /**
     * Заголовок выводится на главной странице.
     *
     * @param bool $mainPage
     * @return HtmlHeader
     */
    public function setMainPage(bool $mainPage): static
    {
        $this->mainPage = $mainPage;
        return $this;
    }

    public function setHeader(string $header): static
    {
        $this->header = $header;
        return $this;
    }

    public function setSubHeader(?string $subHeader): static
    {
        $this->subHeader = $subHeader;
        return $this;
    }

    public function setUseBackup(bool $useBackup): static
    {
        $this->useBackup = $useBackup;
        return $this;
    }

    public function setDrawerIcon(?string $drawerIcon): static
    {
        $this->drawerIcon = $drawerIcon;
        return $this;
    }

    /**
     * Кнопки навигации.
     *
     * @param array HtmlHeaderNavLink $navLinks
     * @return HtmlHeader
     */
    public function setNavLinks($navLinks): static
    {
        $this->navLinks = $navLinks;
        return $this;
    }

    /**
     * Пункты меню.
     *
     * @param array HtmlHeaderMenuItem $menuItems
     * @return HtmlHeader
     */
    public function setMenuItems($menuItems): static
    {
        $this->menuItems = $menuItems;
        return $this;
    }

    private function getSubHeader(): ?string
    {
        return !empty($this->subHeader) ?
            ($this->useBackup ? $this->subHeader . " (" . S::HEADER_PAGE_MAIN_BACKUP . ")" : $this->subHeader) :
            ($this->useBackup ? S::HEADER_PAGE_MAIN_BACKUP : null);
    }

    protected function drawCompat()
    {
        echo "<div id='divHeader'>" . PHP_EOL;

        echo S::TAB;
        echo "<div class='header'>" . PHP_EOL;
        if ($this->mainPage) {
            $mainLogoAlt = S::HEADER_PAGE_MAIN_LOGO_ALT;
            echo S::TAB . S::TAB;
            echo "<img src='/images/logo.jpg' alt='$mainLogoAlt' class='main-logo'>" . PHP_EOL;
        }

        echo S::TAB . S::TAB;
        echo $this->mainPage ? "<h1 class='main-header'>" : "<h1 class='header'>";
        echo $this->header;
        echo "</h1>" . PHP_EOL;

        if (!empty($this->subHeader) || $this->useBackup) {
            $subHeaderClass = $this->mainPage ? "main-header" : "header";

            if ($this->useBackup) {
                $subHeaderClass .= " " . "color-text--error";
            }

            echo S::TAB . S::TAB;
            echo "<h2 class='$subHeaderClass'>";
            echo $this->getSubHeader();
            echo "</h2>" . PHP_EOL;
        }

        echo S::TAB;
        echo '</div> <!-- class="header" -->' . PHP_EOL;

        echo S::TAB;
        echo "<div class='hr'></div>" . PHP_EOL;

        echo '</div> <!-- id="divHeader" -->' . PHP_EOL . PHP_EOL;
    }

    protected function drawNewDesign()
    {
        echo "<header id='divHeader' class='mdl-layout__header'>" . PHP_EOL;

        if (!empty($this->drawerIcon)) {
            echo S::TAB;
            echo "<div class='mdl-layout__drawer-button disabled'>" . PHP_EOL;
            echo S::TAB . S::TAB;
            echo "<span class='material-icons'>$this->drawerIcon</span>" . PHP_EOL;
            echo S::TAB;
            echo '</div>' . PHP_EOL;
        }

        echo S::TAB;
        echo "<div class='mdl-layout__header-row'>" . PHP_EOL;
        echo S::TAB . S::TAB;

        echo "<span data-header class='mdl-layout-title'>";
        echo $this->header;
        echo '</span>' . PHP_EOL;

        echo S::TAB . S::TAB;
        echo "<div class='mdl-layout-spacer'></div>" . PHP_EOL;

        if (!empty($this->navLinks)) {
            echo S::TAB . S::TAB;
            echo '<nav class="mdl-navigation">' . PHP_EOL;

            /** @var HtmlHeaderNavLink $navLink */
            foreach ($this->navLinks as $navLink) {
                echo S::TAB . S::TAB . S::TAB;
                $id = $navLink->getId();
                $idIcon = $id . 'Icon';
                $idText = $id . 'Text';
                $hidden = $navLink->getHidden() ? ' hidden' : '';
                $onClick = $navLink->getOnClick();

                if (isset($onClick)) {
                    echo "<span id='$idIcon' class='material-icons cursor-pointer$hidden' onclick='$onClick'>";
                    echo $navLink->getIcon();
                    echo '</span>' . PHP_EOL;
                    echo S::TAB . S::TAB . S::TAB;
                    echo "<span id='$idText' class='mdl-navigation__link mdl-navigation__link--padding-left cursor-pointer$hidden' onclick='$onClick'>";
                    echo $navLink->getText();
                    echo '</span>' . PHP_EOL;
                }
            }

            echo S::TAB . S::TAB;
            echo '</nav>' . PHP_EOL;
        }

        if (!empty($this->menuItems)) {
            echo S::TAB . S::TAB;
            echo '<span id="menuMore" class="material-icons cursor-pointer">more_vert</span>' . PHP_EOL;
        }

        echo S::TAB;
        echo '</div> <!-- class="mdl-layout__header-row" -->' . PHP_EOL;

        if (!empty($this->subHeader) || $this->useBackup) {
            $subHeaderClass = "mdl-layout-title mdl-layout-title__subtitle";

            if ($this->useBackup) {
                $subHeaderClass .= " " . "color-text--error";
            }

            echo S::TAB;
            echo "<div class='mdl-layout__header-row'>" . PHP_EOL;
            echo S::TAB . S::TAB;
            echo "<span data-header class='$subHeaderClass'>";
            echo $this->getSubHeader();
            echo '</span>' . PHP_EOL;
            echo S::TAB;
            echo '</div> <!-- class="mdl-layout__header-row" -->' . PHP_EOL;
        }

        echo '</header>' . PHP_EOL . PHP_EOL;

        if (isset($this->menuItems)) {
            /** @noinspection HtmlUnknownAttribute */
            echo '<ul id="menuMoreList" class="mdl-menu mdl-menu--bottom-right mdl-js-menu hidden" for="menuMore">' . PHP_EOL;

            /** @var HtmlHeaderMenuItem $menuItem */
            foreach ($this->menuItems as $menuItem) {
                echo S::TAB;
                $id = $menuItem->getId();
                $text = $menuItem->getText();
                $onClick = $menuItem->getOnClick();

                if (isset($onClick)) {
                    echo "<li disabled id='$id' class='mdl-menu__item' onclick='$onClick'>$text</li>" . PHP_EOL;
                }
            }

            echo '</ul>' . PHP_EOL . PHP_EOL;
        }
    }
}
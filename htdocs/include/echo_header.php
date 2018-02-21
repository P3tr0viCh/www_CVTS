<?php
require_once "Strings.php";

use Strings as S;

class NavLink
{
    private $id;
    private $icon;
    private $text;
    private $onClick;
    private $hidden;

    /**
     * NavLink constructor.
     * @param string $id
     * @param string $icon
     * @param string $text
     * @param string $onClick
     * @param bool $hidden
     */
    public function __construct($id, $icon, $text, $onClick, $hidden = false)
    {
        $this->id = $id;
        $this->icon = $icon;
        $this->text = $text;
        $this->onClick = $onClick;
        $this->hidden = $hidden;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getOnClick()
    {
        return $this->onClick;
    }
}

class MenuItem
{
    private $id;
    private $text;
    private $onClick;

    /**
     * MenuItem constructor.
     * @param string $id
     * @param string $text
     * @param string $onClick
     */
    public function __construct($id, $text, $onClick)
    {
        $this->id = $id;
        $this->text = $text;
        $this->onClick = $onClick;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getOnClick()
    {
        return $this->onClick;
    }
}

/**
 * @param bool $newDesign
 * @param bool $mainHeader
 * @param string|null $header
 * @param string|null $subHeader
 * @param array NavLink|null $navLinks
 * @param array MenuItem|null $menuItems
 */
function echoHeader($newDesign, $mainHeader, $header, $subHeader = null, $navLinks = null, $menuItems = null)
{
    if (!$header) {
        $header = S::HEADER_PAGE_MAIN;
    }

    if ($newDesign) {
        $class = $subHeader && !$mainHeader ?
            'mdl-layout__header mdl-layout__header--waterfall' :
            'mdl-layout__header';

        echo "<header id='divHeader' class='$class'>" . PHP_EOL;

        echo S::TAB;
        echo '<div class="mdl-layout__header-row">' . PHP_EOL;
        echo S::TAB . S::TAB;

        echo '<span data-header class="mdl-layout-title">';
        echo $header;
        echo '</span>' . PHP_EOL;

        echo S::TAB . S::TAB;
        echo '<div class="mdl-layout-spacer"></div>' . PHP_EOL;

        if ($navLinks) {
            echo S::TAB . S::TAB;
            echo '<nav class="mdl-navigation">' . PHP_EOL;

            /** @var NavLink $navLink */
            foreach ($navLinks as $navLink) {
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

        if ($menuItems) {
            echo S::TAB . S::TAB;
            echo '<span id="menuMore" class="material-icons cursor-pointer">more_vert</span>' . PHP_EOL;
        }

        echo S::TAB;
        echo '</div> <!-- class="mdl-layout__header-row" -->' . PHP_EOL;

        if ($subHeader) {
            echo S::TAB;
            echo '<div class="mdl-layout__header-row">' . PHP_EOL;
            echo S::TAB . S::TAB;
            echo '<span data-header class="mdl-layout-title mdl-layout-title__subtitle">';
            echo $subHeader;
            echo '</span>' . PHP_EOL;
            echo S::TAB;
            echo '</div> <!-- class="mdl-layout__header-row" -->' . PHP_EOL;
        }

        echo '</header>' . PHP_EOL . PHP_EOL;

        if ($menuItems) {
            echo '<ul id="menuMoreList" class="mdl-menu mdl-menu--bottom-right mdl-js-menu hidden" for="menuMore">' . PHP_EOL;

            /** @var MenuItem $menuItem */
            foreach ($menuItems as $menuItem) {
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
    } else {
        echo '<div id="divHeader">' . PHP_EOL;

        echo S::TAB;
        echo '<div class="header">' . PHP_EOL;
        if ($mainHeader) {
            $mainLogoAlt = S::HEADER_PAGE_MAIN_LOGO_ALT;
            echo S::TAB . S::TAB;
            echo "<img src='/images/logo.jpg' alt='$mainLogoAlt' class='main-logo'>" . PHP_EOL;
        }

        echo S::TAB . S::TAB;
        echo $mainHeader ? '<h1 class="main-header">' : '<h1>';
        echo $header;
        echo '</h1>' . PHP_EOL;

        if ($subHeader) {
            echo S::TAB . S::TAB;
            echo $mainHeader ? '<h2 class="main-header">' : '<h2>';
            echo $subHeader;
            echo '</h2>' . PHP_EOL;
        }

        echo S::TAB;
        echo '</div> <!-- class="header" -->' . PHP_EOL;

        echo S::TAB;
        echo '<div class="hr"></div>' . PHP_EOL;

        echo '</div> <!-- id="divHeader" -->' . PHP_EOL . PHP_EOL;
    }
}
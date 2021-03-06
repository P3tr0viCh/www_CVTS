<?php
require_once "Strings.php";

require_once "HtmlBase.php";

use Strings as S;

class HtmlFooter extends HtmlBase
{
    protected function drawNewDesign()
    {
        echo PHP_EOL;

        echo '<footer id="divFooter" class="mdl-mini-footer">' . PHP_EOL;
        echo S::TAB;
        echo '<div class="mdl-mini-footer--left-section">' . PHP_EOL;
        echo S::TAB . S::TAB;
        echo '<div class="mdl-logo">';

        echo S::FOOTER_LEFT_SECTION;

        echo '</div>' . PHP_EOL;
        echo S::TAB;
        echo '</div>' . PHP_EOL;
        echo S::TAB;
        echo '<div class="mdl-mini-footer--right-section">' . PHP_EOL;
        echo S::TAB . S::TAB;
        echo '<div class="mdl-logo">';

        echo S::FOOTER_RIGHT_SECTION;

        echo '</div>' . PHP_EOL;
        echo S::TAB;
        echo '</div>' . PHP_EOL;
        echo '</footer>' . PHP_EOL;
    }

    protected function drawCompat()
    {
        echo PHP_EOL;

        echo '<div id="divFooter">' . PHP_EOL;

        echo S::TAB;
        echo '<div class="hr"></div>' . PHP_EOL;

        echo S::TAB;
        echo '<div class="footer">' . PHP_EOL;
        echo S::TAB . S::TAB;
        echo '<table class="footer">' . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB;
        echo '<tbody>' . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB;
        echo '<tr>' . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB . S::TAB;
        echo '<td class="footer">';

        echo S::FOOTER_LEFT_SECTION;

        echo '</td>' . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB . S::TAB;
        echo '<td class="footer footer--right-section">';

        echo S::FOOTER_RIGHT_SECTION;

        echo '</td>' . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB;
        echo '</tr>' . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB;
        echo '</tbody>' . PHP_EOL;
        echo S::TAB . S::TAB;
        echo '</table>' . PHP_EOL;
        echo S::TAB;
        echo '</div> <!-- class="footer" -->' . PHP_EOL;

        echo '</div> <!-- id="footer" -->' . PHP_EOL;
    }
}
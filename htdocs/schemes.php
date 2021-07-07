<?php
require_once "include/Links.php";
require_once "include/Strings.php";
require_once "include/MetaInfo.php";
require_once "include/Functions.php";
require_once "include/ParamName.php";
require_once "include/CheckBrowser.php";

require_once "include/echo_html_page.php";

require_once "include/HtmlHeader.php";
require_once "include/HtmlFooter.php";

use Strings as S;

$newDesign = isNewDesign();

echoStartPage();

echoHead($newDesign, S::TITLE_SCHEMES, null, "/javascript/footer.js");

echoStartBody($newDesign);

(new HtmlHeader($newDesign))
    ->setMainPage(false)
    ->setHeader(S::HEADER_PAGE_SCHEMES)
    ->setDrawerIcon("schema")
    ->draw();

echoStartMain($newDesign);

echoStartContent();

function echoImage(bool $newDesign, string $image, string $caption, string $desc)
{
    if ($newDesign) {
        echo "<div class='image-card'>" . PHP_EOL;
        echo S::TAB;
        echo "<div class='mdl-card mdl-shadow--4dp'>" . PHP_EOL;
        echo S::TAB . S::TAB;
        echo "<div class='mdl-card__title'>" . PHP_EOL;
        echo S::TAB . S::TAB. S::TAB;
        echo "<h2 class='mdl-card__title-text'>$caption</h2>" . PHP_EOL;
        echo S::TAB . S::TAB;
        echo "</div>" . PHP_EOL;
        echo S::TAB . S::TAB;
        echo "<div class='mdl-card__media'>" . PHP_EOL;
        echo S::TAB . S::TAB. S::TAB;
        echo "<img src='/images/$image' alt='$desc'>" . PHP_EOL;
        echo S::TAB . S::TAB;
        echo "</div>" . PHP_EOL;
        echo S::TAB;
        echo "</div>" . PHP_EOL;
        echo '</div>' . PHP_EOL;
    } else {
        echo '<div class="image">' . PHP_EOL;
        echo S::TAB;
        echo "<img src='/images/$image' alt='$desc' class=''>" . PHP_EOL;
        echo S::TAB;
        echo '<h2>' . $caption . '</h2>' . PHP_EOL;
        echo '</div>' . PHP_EOL;
    }
}

echoImage($newDesign, "schemes/avitek_vd-30-1-4.png", "ВД-30-1-4", "Схема весов Авитек+ ВД-30-1-4");
echoImage($newDesign, "schemes/avitek_vd-30-2-8.png", "ВД-30-2-8", "Схема весов Авитек+ ВД-30-2-8");
echoImage($newDesign, "schemes/avitek_vd-30-2-12.png", "ВД-30-2-12", "Схема весов Авитек+ ВД-30-2-12");
echoImage($newDesign, "schemes/avitek_avp-vp-sd-150-2.png", "АВП-ВП-СД-150-2", "Схема весов Авитек+ АВП-ВП-СД-150-2");

echoEndContent();

(new HtmlFooter($newDesign))->draw();

echoEndMain($newDesign);

echoEndBody($newDesign, "updateContentMinHeightOnEndBody();");

echoEndPage();
<?php
/**
 * Функции, создающие шаблон страницы.
 *
 * Использование:
 * echoStartPage -- начало страницы.
 *     echoHead -- вывод заголовка
 *     echoUseScripts -- подключение скриптов.
 *     echoStartBody -- начало тела страницы.
 *         HtmlHeader -- вывод хизера (файл {@link [HtmlHeader.php]}).
 *         HtmlDrawer -- вывод главного меню (файл {@link [HtmlDrawer.php]}).
 *         echoStartMain -- начало главного блока.
 *             echoStartContent -- начало контента.
 *                 Контент.
 *             echoEndContent -- конец контента.
 *             HtmlFooter -- вывод футтера (файл {@link [HtmlFooter.php]}).
 *         echoEndMain -- конец главного блока.
 *     echoEndBody.
 * echoEndPage.
 *
 * Если HtmlFooter использовать после echoEndMain, футтер будет всегда внизу окна, иначе в конце контента.
 */

require_once "MetaInfo.php";
require_once "Strings.php";

use Strings as S;

function echoStartPage()
{
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" ' .
        '"http://www.w3.org/TR/html4/loose.dtd">' . PHP_EOL . PHP_EOL;
//    TODO echo '<!DOCTYPE html>' . PHP_EOL . PHP_EOL;
    echo '<html lang="RU">' . PHP_EOL;
}

/**
 * @param string $name
 * @param string $value
 * @param string $content
 */
function echoMeta(string $name, string $value, string $content)
{
    echo S::TAB;
    echo "<meta $name=\"$value\" content=\"$content\">" . PHP_EOL;
}

/**
 * @param bool $newDesign
 * @param string|null $title
 * @param string|string[]|null $styles
 * @param string|string[]|null $javaScripts
 * @param bool $default Вывод стандартных стилей и скриптов
 */
function echoHead(bool $newDesign, string $title = null, array|string $styles = null, array|string $javaScripts = null, bool $default = true)
{
    /** @noinspection HtmlRequiredTitleElement */
    echo '<head>' . PHP_EOL;

    if ($title) {
        echo S::TAB;
        echo '<title>' . $title . '</title>' . PHP_EOL . PHP_EOL;
    }

    echoMeta("http-equiv", "Content-Type", "text/html; charset=utf-8");
    echoMeta("http-equiv", "Content-Language", "ru");
    echoMeta("http-equiv", "Cache-Control", "public");
    echo PHP_EOL;

    echoMeta("name", "google", "notranslate");
    echo PHP_EOL;

    echoMeta("name", "author", MetaInfo::AUTHOR);
    echoMeta("name", "company", MetaInfo::COMPANY);
    echoMeta("name", "version", MetaInfo::VERSION);
    echoMeta("name", "creation", MetaInfo::CREATION);
    echo PHP_EOL;

    if ($default) {
        echo S::TAB;
        echo '<link rel="icon" href="/images/logo.png" type="image/png">' . PHP_EOL;

        echo S::TAB;
        if ($newDesign) {
            echo '<link rel="icon" href="/images/favicon.png" type="image/png">' . PHP_EOL;
            echo S::TAB;
            echo '<link rel="shortcut icon" href="/images/favicon.png" type="image/png">' . PHP_EOL;
        } else {
            echo '<link rel="icon" href="/images/favicon.ico" type="image/x-icon">' . PHP_EOL;
            echo S::TAB;
            echo '<link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon">' . PHP_EOL;
        }

        echo PHP_EOL;
    }

    if ($default) {
        echo S::TAB;
        echo '<link rel="stylesheet" href="/styles/common.css">' . PHP_EOL;

        echo S::TAB;
        if ($newDesign) {
            echo '<link rel="stylesheet" href="/mdl/material.min.css">' . PHP_EOL;
            echo S::TAB;
            echo '<link rel="stylesheet" href="/styles/common_new_design.css">' . PHP_EOL;
            echo S::TAB;
            echo '<link rel="stylesheet" href="/styles/scripts_enabled.css">' . PHP_EOL;
            echo S::TAB;
            echo '<link rel="stylesheet" href="/fonts/roboto/roboto.css">' . PHP_EOL;
            echo S::TAB;
            echo '<link rel="stylesheet" href="/fonts/robotottf/roboto.css">' . PHP_EOL;
            echo S::TAB;
            echo '<link rel="stylesheet" href="/materialicons/materialicons.css">' . PHP_EOL;
        } else {
            echo '<link rel="stylesheet" href="/styles/common_compat.css">' . PHP_EOL;
        }
    }

    if ($styles) {
        if (is_string($styles)) {
            echo S::TAB;
            echo "<link rel='stylesheet' href='$styles'>" . PHP_EOL;
        } elseif (is_array($styles)) {
            foreach ($styles as $style) {
                echo S::TAB;
                echo "<link rel='stylesheet' href='$style'>" . PHP_EOL;
            }
        }
    }

    if ($default) {
        echo PHP_EOL;
        echo S::TAB;
        if ($newDesign) {
            echo '<script src="/mdl/material.min.js" type="text/javascript"></script>' . PHP_EOL;
        } else {
            echo '<script type="text/javascript">';
            echo PHP_EOL;
            echo S::TAB . S::TAB;
            echo "document.write('";
            echo '<link rel="stylesheet" href="/styles/scripts_enabled.css" type="text/css">';
            echo "');" . PHP_EOL;
            echo S::TAB;
            echo '</script>';
            echo PHP_EOL;
        }
    }

    if ($javaScripts) {
        if (is_string($javaScripts)) {
            echo S::TAB;
            echo "<script src='$javaScripts' type='text/javascript'></script>" . PHP_EOL;
        } elseif (is_array($javaScripts)) {
            foreach ($javaScripts as $script) {
                echo S::TAB;
                echo "<script src='$script' type='text/javascript'></script>" . PHP_EOL;
            }
        }
    }

    echo '</head>' . PHP_EOL . PHP_EOL;
}

function echoStartBody($newDesign, $onLoad = null, $class = null)
{
    if ($onLoad) {
        $onLoad = " onload='$onLoad'";
    }

    if ($class) {
        $class = " class='$class'";
    }

    echo "<body$onLoad$class>" . PHP_EOL;

    if ($newDesign) {
        echo '<div class="mdl-layout mdl-js-layout mdl-layout--overlay-drawer-button">' . PHP_EOL;
    }
}

function echoStartMain($newDesign)
{
    if ($newDesign) {
        echo '<main class="mdl-layout__content">' . PHP_EOL;
    }
}

/**
 * Начало контента.
 *
 * Если параметр $hidden равен true, контент не отображается.
 * Необходимо отобразить его после полной загрузки страницы.
 *
 * @param bool $hidden
 */
function echoStartContent(bool $hidden = false)
{
    if ($hidden) {
        echo "<div id='divContent' class='page-content' style='display: none'>" . PHP_EOL;
    } else {
        echo "<div id='divContent' class='page-content'>" . PHP_EOL;
    }
}

function echoEndContent()
{
    echo "</div> <!-- id='divContent' class='page-content' -->" . PHP_EOL;
}

function echoEndMain($newDesign)
{
    if ($newDesign) {
        echo '</main>' . PHP_EOL;
    }
}

function echoErrorPage(string $error, ?string $errorDetails)
{
    echo '<div class="div-center-outer">' . PHP_EOL;
    echo S::TAB;
    echo '<div class="div-center-middle">' . PHP_EOL;
    echo S::TAB . S::TAB;
    echo '<div class="div-center-inner">' . PHP_EOL;

    echo S::TAB . S::TAB . S::TAB;
    echo '<h1 class="result-message color-text--error">' . $error . '</h1>' . PHP_EOL;

    if ($errorDetails) {
        echo S::TAB . S::TAB . S::TAB;
        echo '<h2 class="result-message color-text--secondary">' . $errorDetails . '</h2>' . PHP_EOL;
    }

    echo S::TAB . S::TAB;
    echo '</div>' . PHP_EOL;
    echo S::TAB;
    echo '</div>' . PHP_EOL;
    echo '</div>' . PHP_EOL;
}

function echoJSDisabled()
{
    echo PHP_EOL;
    echo "<noscript>" . PHP_EOL;

    echo '<div class="div-center-outer--center bk-color--white">' . PHP_EOL;
    echo S::TAB;
    echo '<div class="div-center-middle">' . PHP_EOL;
    echo S::TAB . S::TAB;
    echo '<div class="div-center-inner">' . PHP_EOL;

    echo S::TAB . S::TAB . S::TAB;
    echo '<h1 class="result-message color-text--error">' . S::ERROR_JS_DISABLED . '</h1>' . PHP_EOL;

    echo S::TAB . S::TAB . S::TAB;
    echo '<h2 class="result-message color-text--secondary">' . S::ERROR_JS_DISABLED_DETAILS . '</h2>' . PHP_EOL;

    echo S::TAB . S::TAB;
    echo '</div>' . PHP_EOL;
    echo S::TAB;
    echo '</div>' . PHP_EOL;
    echo '</div>' . PHP_EOL;

    echo "</noscript>" . PHP_EOL;
}

function echoEndBody($newDesign, $endScript = null)
{
    if ($newDesign) {
        echo "</div> <!-- class='mdl-layout' -->" . PHP_EOL;
    }

    if ($endScript) {
        echo PHP_EOL;
        echo "<script type='text/javascript'>";
        echo PHP_EOL;

        if (is_string($endScript)) {
            echo S::TAB;
            echo $endScript . PHP_EOL;
        } elseif (is_array($endScript)) {
            foreach ($endScript as $script) {
                echo S::TAB;
                echo $script . PHP_EOL;
            }
        }

        echo "</script>";
        echo PHP_EOL . PHP_EOL;
    }

    echo '</body>' . PHP_EOL;
}

function echoEndPage()
{
    echo '</html>';
}
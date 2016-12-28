<?php
/**
 * Функции, создающие шаблон страницы.
 *
 * Использование:
 * echoStartPage -- начало страницы.
 *     echoHead -- вывод заголовка
 *     echoUseScripts -- подключение скриптов.
 *     echoStartBody -- начало тела страницы.
 *         echoHeader -- вывод хизера (файл {@link [echo_header.php]}).
 *         echoDrawer -- вывод главного меню (файл {@link [echo_drawer.php]}).
 *         echoStartMain -- начало главного блока.
 *             echoStartContent -- начало контента.
 *                 Контент.
 *             echoEndContent -- конец контента.
 *             echoFooter -- вывод футтера (файл {@link [echo_footer.php]}).
 *         echoEndMain -- конец главного блока.
 *     echoEndBody.
 * echoEndPage.
 *
 * Если echoFooter использовать после echoEndMain, футтер будет всегда внизу окна, иначе в конце контента.
 */

require_once "MetaInfo.php";
require_once "Strings.php";

use Strings as S;

function echoStartPage()
{
    echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" ' .
        '"http://www.w3.org/TR/html4/loose.dtd">' . PHP_EOL . PHP_EOL;
    echo '<html>' . PHP_EOL;
}

/**
 * @param string $name
 * @param string $value
 * @param string $content
 */
function echoMeta($name, $value, $content)
{
    echo S::TAB;
    echo "<meta $name=\"$value\" content=\"$content\">" . PHP_EOL;
}

/**
 * @param bool $newDesign
 * @param null|string $title
 * @param null|string|string[] $styles
 * @param null|string|string[] $javaScripts
 * @param null|string|string[] $oldIEStyles
 */
function echoHead($newDesign, $title = null, $styles = null, $javaScripts = null, $oldIEStyles = null)
{
    echo '<head>' . PHP_EOL;

    if ($title) {
        echo S::TAB;
        echoTitle($title);
    }

    echoMeta("http-equiv", "Content-Type", "text/html; charset=utf-8");
    echoMeta("http-equiv", "Content-Language", "ru");
    echoMeta("http-equiv", "Cache-Control", "no-cache");
    echo PHP_EOL;

    echoMeta("name", "google", "notranslate");
    echo PHP_EOL;

    echoMeta("name", "author", MetaInfo::AUTHOR);
    echoMeta("name", "version", MetaInfo::VERSION);
    echoMeta("name", "creation", MetaInfo::CREATION);
    echo PHP_EOL;

    echo S::TAB;
    echo '<link rel="icon" href="/images/logo.png" type="image/png">' . PHP_EOL;

    if ($newDesign) {
        echo S::TAB;
        echo '<link rel="icon" href="/images/favicon.png" type="image/png">' . PHP_EOL;
        echo S::TAB;
        echo '<link rel="shortcut icon" href="/images/favicon.png" type="image/png">' . PHP_EOL;
    } else {
        echo S::TAB;
        echo '<link rel="icon" href="/images/favicon.ico" type="image/x-icon">' . PHP_EOL;
        echo S::TAB;
        echo '<link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon">' . PHP_EOL;
    }

    echo PHP_EOL;
    echo S::TAB;
    echo '<link rel="stylesheet" href="/styles/common.css">' . PHP_EOL;

    if ($newDesign) {
        echo S::TAB;
        echo '<link rel="stylesheet" href="/mdl/material.min.css">' . PHP_EOL;
        echo S::TAB;
        echo '<link rel="stylesheet" href="/styles/new_design.css">' . PHP_EOL;
        echo S::TAB;
        echo '<link rel="stylesheet" href="/styles/scripts_enabled.css">' . PHP_EOL;
        echo S::TAB;
        echo '<link rel="stylesheet" href="/fonts/roboto/roboto.css">' . PHP_EOL;
        echo S::TAB;
        echo '<link rel="stylesheet" href="/materialicons/materialicons.css">' . PHP_EOL;
    } else {
        echo S::TAB;
        echo '<link rel="stylesheet" href="/styles/compat.css">' . PHP_EOL;
        echo S::TAB;
        echo "<!--[if lt IE 9]>" . PHP_EOL;
        echo S::TAB . S::TAB;
        echo '<link rel="stylesheet" href="/styles/compat_ie.css">' . PHP_EOL;
        echo S::TAB;
        echo "<![endif]-->" . PHP_EOL;
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

    if ($oldIEStyles) {
        echo S::TAB;
        echo "<!--[if lt IE 9]>" . PHP_EOL;
        if (is_string($oldIEStyles)) {
            echo S::TAB . S::TAB;
            echo "<link rel='stylesheet' href='$oldIEStyles'>" . PHP_EOL;
        } elseif (is_array($oldIEStyles)) {
            foreach ($oldIEStyles as $style) {
                echo S::TAB . S::TAB;
                echo "<link rel='stylesheet' href='$style'>" . PHP_EOL;
            }
        }
        echo S::TAB;
        echo "<![endif]-->" . PHP_EOL;
    }

    echo PHP_EOL;
    if ($newDesign) {
        echo S::TAB;
        echo '<script src="/mdl/material.min.js" type="text/javascript"></script>' . PHP_EOL;
    } else {
        echo S::TAB;
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

function echoTitle($title)
{
    echo '<title>' . $title . '</title>' . PHP_EOL . PHP_EOL;
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
function echoStartContent($hidden = false)
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

/**
 * @param string $error
 * @param string $errorDetails
 */
function echoErrorPage($error, $errorDetails)
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
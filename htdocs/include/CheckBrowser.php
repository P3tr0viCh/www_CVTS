<?php
require_once "builders/href_builder/Builder.php";

class CheckBrowser
{
    const COMPATIBLE_MIN_VERSION_IE = 11;
    const COMPATIBLE_MIN_VERSION_EDGE = 13;
    const COMPATIBLE_MIN_VERSION_CHROME = 46;

    /**
     * Проверяет версию браузера
     *
     * @return bool
     */
    public static function isCompatibleVersion()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];

        if (preg_match("/(Edge)\/([0-9.]+)/", $agent, $edge)) {
            return $edge[2] >= self::COMPATIBLE_MIN_VERSION_EDGE;
        }

        preg_match("/(Edge|Trident|MSIE|Opera|Firefox|Chrome|Version|Opera Mini|Netscape|Konqueror|SeaMonkey|Camino|Minefield|Iceweasel|K-Meleon|Maxthon)(?:\/| )([0-9.]+)/",
            $agent, $browserInfo);

        list(, $browser, $version) = $browserInfo;

        if ($browser == 'Chrome') {
            return $version >= self::COMPATIBLE_MIN_VERSION_CHROME;
        }

        if ($browser == 'Edge') {
            return $version >= self::COMPATIBLE_MIN_VERSION_EDGE;
        }

        if ($browser == 'Trident') {
            if (preg_match("/(rv:)(?:\/|)([0-9.]+)/", $agent, $ie)) {
                return $ie[2] >= self::COMPATIBLE_MIN_VERSION_IE;
            } else {
                return false;
            }
        }

        return false;
    }

    /**
     * Проверяет браузер на совместимость.
     *
     * @param bool $newDesign
     * @param bool $showIncompatibleBrowserPage
     * Если true -- выполняется переход на страницу с сообщением о несовместимости браузера,
     * иначе -- меняет переменную $newDesign на false.
     */
    public static function check(&$newDesign, $showIncompatibleBrowserPage)
    {
        if ($newDesign) {
            if (!self::isCompatibleVersion()) {
                if ($showIncompatibleBrowserPage) {
                    header("Location: " . "/incompatible_browser.php");
                    die();
                } else {
                    $newDesign = false;
                }
            }

            echo '<noscript>' . PHP_EOL;
            echo Strings::TAB;
            if ($showIncompatibleBrowserPage) {
                $url = "/incompatible_browser.php";
            } else {
                $url = \HrefBuilder\Builder::getInstance()->setUrl(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
                    ->setParam(ParamName::NEW_DESIGN, false)
                    ->build();
            }
            echo "<meta http-equiv='refresh' content='0;url=$url'>" . PHP_EOL;
            echo '</noscript>' . PHP_EOL . PHP_EOL;
        }
    }
}
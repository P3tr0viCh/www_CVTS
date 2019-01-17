<?php
require_once "builders/href_builder/Builder.php";

class CheckBrowser
{
    const COMPATIBLE_MIN_VERSION_IE = 11;
    const COMPATIBLE_MIN_VERSION_EDGE = 13;
    const COMPATIBLE_MIN_VERSION_CHROME = 46;

    // debug
    const SHOW_AGENT = false;

    /**
     * Проверяет версию браузера на совместимость с новым интерфейсом
     *
     * @return bool
     */
    public static function isCompatibleVersion()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];

        if (self::SHOW_AGENT) {
            echo $agent . "<br>";
        }

        if ($agent == "") return false;

        if (preg_match("/(Edge)\/([0-9.]+)/", $agent, $edge)) {
            if (self::SHOW_AGENT) {
                echo "Edge: " . $edge[2] . "<br>";
            }

            return $edge[2] >= self::COMPATIBLE_MIN_VERSION_EDGE;
        }

        if (!preg_match("/(Trident|Chrome)(?:\/| )([0-9.]+)/", $agent, $browserInfo)) {
            if (self::SHOW_AGENT) {
                echo "Unknown" . "<br>";
            }

            return false;
        };

        list(, $browser, $version) = $browserInfo;

        if ($browser == 'Chrome') {
            if (self::SHOW_AGENT) {
                echo "Chrome: " . $version . "<br>";
            }

            return $version >= self::COMPATIBLE_MIN_VERSION_CHROME;
        }

        if ($browser == 'Trident') {
            if (preg_match("/(rv:)(?:\/|)([0-9.]+)/", $agent, $ie)) {
                if (self::SHOW_AGENT) {
                    echo "IE: " . $ie[2] . "<br>";
                }

                return $ie[2] >= self::COMPATIBLE_MIN_VERSION_IE;
            } else {
                if (self::SHOW_AGENT) {
                    echo "Unknown" . "<br>";
                }

                return false;
            }
        }

        if (self::SHOW_AGENT) {
            echo "Unknown" . "<br>";
        }

        return false;
    }
}
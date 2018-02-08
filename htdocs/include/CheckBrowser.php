<?php
require_once "builders/href_builder/Builder.php";

class CheckBrowser
{
    const COMPATIBLE_MIN_VERSION_IE = 11;
    const COMPATIBLE_MIN_VERSION_EDGE = 13;
    const COMPATIBLE_MIN_VERSION_CHROME = 46;

    /**
     * Проверяет версию браузера на совместимость с новым интерфейсом
     *
     * @return bool
     */
    public static function isCompatibleVersion()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];

        if ($agent == "") return false;

//        echo $agent . "<br>";

        if (preg_match("/(Edge)\/([0-9.]+)/", $agent, $edge)) {
            return $edge[2] >= self::COMPATIBLE_MIN_VERSION_EDGE;
        }

        if (!preg_match("/(Trident|Chrome)(?:\/| )([0-9.]+)/",
            $agent, $browserInfo)) {
            return false;
        };

        list(, $browser, $version) = $browserInfo;

        if ($browser == 'Chrome') {
            return $version >= self::COMPATIBLE_MIN_VERSION_CHROME;
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
}
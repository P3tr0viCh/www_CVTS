<?php
/*
 * Проверка на вхождение пользователя сайта в группу авторизованных пользователей,
 * которым доступен расширенный функционал.
 *
 * Список логинов хранится в файле
 * с именем POWER_USERS, находящегося в родительском для htdocs каталоге.
 *
 * Одна строка -- один логин.
*/

require_once "FileNames.php";

class CheckUser
{
    private static array $powerUsers;

    private static function readPowerUsersFile()
    {
        self::$powerUsers = explode(PHP_EOL, file_get_contents("../" . FileNames::POWER_USERS));
    }

    public static function isPowerUser(): bool
    {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            return true;
        }

        if (!isset(self::$powerUsers)) {
            self::readPowerUsersFile();
        }

        foreach (self::$powerUsers as $powerUser) {
            if (strcasecmp($powerUser, $_SERVER['PHP_AUTH_USER']) == 0) {
                return true;
            }
        }

        return false;
    }
}
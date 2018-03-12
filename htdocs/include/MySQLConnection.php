<?php
/*
 * Настройки подключения к серверу MySQL читаются из файла
 * с именем MYSQL_CONNECTION, находящегося в родительском для htdocs каталоге.
 *
 * Файл MYSQL_CONNECTION содержит шесть строк:
 * первая -- адрес сервера;
 * вторая -- имя пользователя;
 * третья -- пароль пользователя;
 * 4-ая   -- адрес резервного сервера;
 * 5-ая   -- имя пользователя на резервном сервере;
 * 6-ая   -- пароль пользователя на резервном сервере.
 *
 * Пример:
 * 127.0.0.1:3306:/hd0/tmp/mysql.sock
 * username
 * password
 * localhost
 * local_username
 * local_password
*/

require_once "Database.php";
require_once "FileNames.php";

class MySQLConnection
{
    /**
     * @param bool $use_backup
     * @param null|string $dbName
     * @return mysqli|null
     */
    public static function getInstance($use_backup = false, $dbName = null)
    {
        $handle = @fopen("../" . FileNames::MYSQL_CONNECTION, "r");

        $hosts = array();
        $usernames = array();
        $passwords = array();

        if ($handle) {
            $hosts[] = trim(fgets($handle, 255));
            $usernames[] = trim(fgets($handle, 255));
            $passwords[] = trim(fgets($handle, 255));

            $hosts[] = trim(fgets($handle, 255));
            $usernames[] = trim(fgets($handle, 255));
            $passwords[] = trim(fgets($handle, 255));

            fclose($handle);

            $i = $use_backup ? 1 : 0;

            if (!$hosts[$i] || !$usernames[$i] || !$passwords[$i]) {
                return null;
            }
        } else {
            return null;
        }

        if (empty($dbName)) {
            $dbName = Database\Info::WDB;
        }

        $mysqli = @new mysqli($hosts[$i], $usernames[$i], $passwords[$i], $dbName);

        if (!$mysqli->connect_errno) {
            $mysqli->set_charset(Database\Info::CHARSET);
        }

        return $mysqli;
    }
}
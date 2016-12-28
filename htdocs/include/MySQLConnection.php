<?php
/*
 * Настройки подключения к серверу MySQL читаются из файла
 * с именем MYSQL_CONNECTION, находящегося в родительском для htdocs каталоге.
 *
 * Файл MYSQL_CONNECTION содержит три строки:
 * первая -- адрес сервера;
 * вторая -- имя пользователя;
 * третья -- пароль пользователя.
 *
 * Пример:
 * 127.0.0.1:3306:/hd0/tmp/mysql.sock
 * username
 * password
*/

require_once "Database.php";

class MySQLConnection
{
    /**
     * @param null|string $dbName
     * @return mysqli|null
     */
    public static function getInstance($dbName = null)
    {
        $handle = @fopen("../MYSQL_CONNECTION", "r");

        if ($handle) {
            $host = trim(fgets($handle, 255));
            $username = trim(fgets($handle, 255));
            $password = trim(fgets($handle, 255));

            fclose($handle);

            if (!$host || !$username || !$password) {
                return null;
            }
        } else {
            return null;
        }

        if (empty($dbName)) {
            $dbName = Database\Info::WDB;
        }

        $mysqli = @new mysqli($host, $username, $password, $dbName);

        if (!$mysqli->connect_errno) {
            $mysqli->set_charset(Database\Info::CHARSET);
        }

        return $mysqli;
    }
}
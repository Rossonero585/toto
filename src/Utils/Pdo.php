<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 14/10/17
 * Time: 12:37
 */

namespace Utils;

class Pdo
{
    const USER = 'root';

    const PASSWORD = 'fgtkmcby';

    /**
     * @var \PDO
     */
    private static $pdo;

    public static function getPdo($root = false) : \PDO
    {
        if ($root) {
            self::createRootConnection();
        }
        else {
            self::createConnection();
        }

        self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        self::$pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        return self::$pdo;
    }

    public static function closeConnection()
    {
        self::$pdo = null;
    }

    private static function getTotoId()
    {
        $opt = getopt('t:');

        if (isset($_SESSION['toto_id'])) {
            return $_SESSION['toto_id'];
        }
        else if (isset($_REQUEST['toto_id'])) {
            return $_REQUEST['toto_id'];
        }
        else if (isset($opt['t'])) {
            return $opt['t'];
        }
        else {
            return null;
        }
    }

    private static function createConnection()
    {
        $totoId = self::getTotoId();

        $dsn = "mysql:dbname=toto_$totoId;host=127.0.0.1;charset=utf8;port=3306";

        self::$pdo = new \PDO($dsn, self::USER, self::PASSWORD);
    }

    private static function createRootConnection()
    {
        $dsn = "mysql:host=127.0.0.1;charset=utf8";

        self::$pdo = new \PDO($dsn, self::USER, self::PASSWORD);
    }
}
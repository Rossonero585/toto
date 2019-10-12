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

        $dsn = "mysql:dbname=toto_$totoId;host=".$_ENV['DB_HOST'].";charset=utf8;port=".$_ENV['DB_PORT'];

        self::$pdo = new \PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    }

    private static function createRootConnection()
    {
        $dsn = "mysql:host=".$_ENV['DB_HOST'].";charset=utf8;port=".$_ENV['DB_PORT'];

        self::$pdo = new \PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    }
}

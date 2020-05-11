<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 14/10/17
 * Time: 12:37
 */

namespace Utils;

use Helpers;

class Pdo
{
    /**
     * @var PdoWrapper
     */
    private static $rootPdo;

    /**
     * @var PdoWrapper
     */
    private static $pdo;

    public static function getPdo($root = false) : PdoWrapper
    {
        if ($root) {
            if (!self::$rootPdo) self::createRootConnection();
            return self::$rootPdo;
        }
        else {
            if (!self::$pdo) self::createConnection();
            return self::$pdo;
        }
    }

    public static function closeConnection()
    {
        self::$pdo = null;
    }

    private static function getTotoId()
    {
        return Helpers\getTotoId();
    }

    private static function createConnection()
    {
        $totoId = self::getTotoId();

        $dsn = "mysql:dbname=toto_$totoId;host=".$_ENV['DB_HOST'].";charset=utf8;port=".$_ENV['DB_PORT'];

        self::$pdo = new PdoWrapper($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

        self::applyAttributes(self::$pdo);
    }

    private static function createRootConnection()
    {
        $dsn = "mysql:host=".$_ENV['DB_HOST'].";charset=utf8;port=".$_ENV['DB_PORT'];

        self::$rootPdo = new PdoWrapper($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

        self::applyAttributes(self::$rootPdo);
    }


    private static function applyAttributes(PdoWrapper $pdo)
    {
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }
}

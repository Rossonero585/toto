<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 04/01/20
 * Time: 10:48
 */

namespace Controllers;

use Utils\Pdo;

class ViewController
{
    public function getAll()
    {
        /** @var \PDO $pdo */
        $pdo = Pdo::getPdo(true);

        $st = $pdo->query("SHOW DATABASES LIKE 'toto_%'");

        $databases = $st->fetchAll();

        $s = "";

        foreach ($databases as $db) {

            $dbName = array_shift($db);

            $pdo->exec("USE $dbName");

            $tempArr = explode("_", $dbName);

            $totoId = (int)array_pop($tempArr);

            $toto = $pdo->query("SELECT * FROM toto")->fetch();

            $startDate = $toto['start_date'];

            $betItems = $pdo->query("SELECT * FROM bet_items")->fetchAll();

            foreach ($betItems as $item) {

                $bet = $item['bet'];
                $money = $item['money'];
                $income = $item['income'];
                $ev = $item['ev'];
                $probability = $item['probability'];

                $s .= "$totoId;$startDate;$bet;$money;$income;$ev;$probability" . PHP_EOL;

            }

        }

        echo $s;
    }
}
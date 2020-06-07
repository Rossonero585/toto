<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 04/01/20
 * Time: 10:48
 */

namespace Controllers;

use Builders\EventBuilder;
use Builders\Providers\EventFromArray;
use Helpers\EventsHelper;
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

            if (mb_strlen($dbName) == 12) {

                try {

                    $pdo->exec("USE $dbName");

                    $tempArr = explode("_", $dbName);

                    $totoId = (int)array_pop($tempArr);

                    $toto = $pdo->query("SELECT * FROM toto")->fetch();

                    $startDate = $toto['start_date'];

                    $betItems = $pdo->query("SELECT * FROM bet_items")->fetchAll();

                    $bet = 0;

                    $income = 0;

                    foreach ($betItems as $item) {

                        $bet += (float)$item['money'];
                        $income += (float)$item['income'];
                    }


                    $s .= "$totoId;$startDate;$bet;$bet;$income" . PHP_EOL;
                }
                catch (\Exception $exception) {}
            }
        }

        echo $s;
    }


    public function dumpAllEv()
    {
        /** @var \PDO $pdo */
        $pdo = Pdo::getPdo(true);

        $st = $pdo->query("SHOW DATABASES LIKE 'toto_%'");

        $databases = $st->fetchAll();

        $s = "";

        foreach ($databases as $db) {

            $dbName = array_shift($db);

            if (mb_strlen($dbName) == 12) {

                try {

                    $pdo->exec("USE $dbName");

                    $tempArr = explode("_", $dbName);

                    $totoId = (int)array_pop($tempArr);

                    $toto = $pdo->query("SELECT * FROM toto")->fetch();

                    $deviation = $toto['pool_deviation'];

                    $betItems = $pdo->query("SELECT * FROM bet_items WHERE ev IS NOT NULL")->fetchAll();

                    $events = $pdo->query("SELECT * FROM events WHERE 1 = 1")->fetchAll();

                    $eventsHelper = new EventsHelper(array_map(function (array $event) {
                        return EventBuilder::createEvent(new EventFromArray($event));
                    }, $events));

                    foreach ($betItems as $item) {

                        $ev = $item['ev'];

                        $p = $item['probability'];

                        $raw_p = $eventsHelper->calculateProbabilityOfAllEvents(str_split($item['bet']));

                        $s .= "$totoId;$deviation;$ev;$p;$raw_p" . PHP_EOL;
                    }

                }
                catch (\Exception $exception) {}
            }
        }

        echo $s;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 15/10/17
 * Time: 21:18
 */
namespace Controllers;

use Builders\EventBuilder;
use Builders\Providers\EventFromWeb;
use Builders\Providers\TotoFromWeb;
use Builders\TotoBuilder;
use Helpers\ArrayHelper;
use Models\BreakDown;
use Models\BreakDownItem;
use Repositories\EventRepository;
use Repositories\PoolRepository;
use Repositories\PreparedResultRepository;
use Repositories\TotoRepository;
use Utils\Pdo;

class UpdateController
{
    const ASSET_PATH = "./assets";

    const BET_CITY = "https://hdr.betcity.ru";

    public function initAction($totoId)
    {
        if (!$totoId) {
            throw new \Exception("totoId is not defined");
        }

        $pdo = Pdo::getPdo(true);

        $dbName = "toto_".$totoId;

        $query = file_get_contents(self::ASSET_PATH."/toto.sql");

        $query = str_replace("%toto_db_name%", $dbName, $query);

        $pdo->exec($query);
    }

    public function insertEventAction($totoId)
    {
        if (!$totoId) {
            throw new \Exception("totoId is not defined");
        }

        $jsonToto = json_decode(file_get_contents(self::BET_CITY."/d/se/one?id=$totoId"));

        $totoEvents = $jsonToto->reply->toto->out;

        $totoRepository = new TotoRepository();

        $toto = TotoBuilder::createToto(new TotoFromWeb($jsonToto));

        $totoRepository->addToto($toto);

        $eventRepository = new EventRepository();

        foreach ($totoEvents as $key => $jsonEvent)
        {
            $event = EventBuilder::createEvent(new EventFromWeb($jsonEvent, ++$key));

            $eventRepository->addEvent($event);
        }
    }

    public function insertBetsAction($totoId)
    {
        if (!$totoId) {
            throw new \Exception("totoId is not defined");
        }

        $text = file_get_contents(self::BET_CITY."/supex/dump/$totoId.txt");

        $totoRepository = new PoolRepository();

        $totoRepository->insertFromFile($text);
    }

    public function updateBreakDownsAction()
    {
        $testedPool = [
            [1, 2, 1, 'x', 1, 1, 1, 1, 2, 1, 2, 2, 1, 2, 50],
            [1, 2, 1, 'x', 1, 1, 1, 1, 2, 1, 2, 2, 1, 2, 50],
            [1, 2, 1, 'x', 1, 1, 1, 1, 2, 1, 2, 2, 1, 2, 50],
            [1, 2, 1, 'x', 1, 1, 1, 1, 2, 1, 2, 2, 1, 2, 50]
        ];

        $preparedResultRepository = new PreparedResultRepository();

        $totoRepository = new TotoRepository();

        $toto = $totoRepository->getToto();

        $minCountForWin = $toto->getMinWinnerCount();

        $cursor = $preparedResultRepository->getAllPool();

        $requestToUpdate = [];

        while ($row = $cursor->fetch(\PDO::FETCH_ASSOC)) {

            $breakDown = array_pop($row);

            if ($breakDown) {
                $breakDown = unserialize($breakDown);
            }
            else {
                $breakDown = new BreakDown();
            }

            $row = [
                $row['r1'],
                $row['r2'],
                $row['r3'],
                $row['r4'],
                $row['r5'],
                $row['r6'],
                $row['r7'],
                $row['r8'],
                $row['r9'],
                $row['r10'],
                $row['r11'],
                $row['r12'],
                $row['r13'],
                $row['r14'],
            ];

            foreach ($testedPool as $item) {
                $bet = array_pop($item);
                $count = ArrayHelper::countMatchResult($item, $row);
                if ($minCountForWin <= $count) {
                    $breakDownItem = new BreakDownItem($count, $bet);
                    $breakDown->addBreakDownItem($breakDownItem);
                }
            }

            if ($breakDown->getCountItems())
            {
                $row['break_down'] = serialize($breakDown);
                $requestToUpdate[] = $row;
            }
        }

        $preparedResultRepository->updateBreakDown($requestToUpdate);

    }

}
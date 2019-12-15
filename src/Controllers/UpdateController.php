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
use Models\Bet;
use Models\BreakDown;
use Models\BreakDownItem;
use Repositories\BetsRepository;
use Repositories\EventRepository;
use Repositories\PoolRepository;
use Repositories\PreparedResultRepository;
use Repositories\Repository;
use Repositories\TotoRepository;
use Utils\Pdo;

class UpdateController
{
    const ASSET_PATH = "assets";

    const BET_CITY = "https://hdr.betcity.ru";

    public function initAction($totoId)
    {
        if (!$totoId) {
            throw new \Exception("totoId is not defined");
        }

        $pdo = Pdo::getPdo(true);

        $dbName = "toto_".$totoId;

        $query = file_get_contents(ROOT_DIR.DIRECTORY_SEPARATOR.self::ASSET_PATH.DIRECTORY_SEPARATOR."toto.sql");

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

        /** @var TotoRepository $totoRepository */
        $totoRepository = Repository::getRepository(TotoRepository::class);

        $toto = TotoBuilder::createToto(new TotoFromWeb($jsonToto));

        $totoRepository->addToto($toto);

        /** @var EventRepository $eventRepository */
        $eventRepository = Repository::getRepository(EventRepository::class);

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

        /** @var PoolRepository $poolRepository */
        $poolRepository = Repository::getRepository(PoolRepository::class);

        $poolRepository->insertFromFile($text);
    }


    public function updateBetsEv()
    {
        $cc = new CalculationController();

        /** @var BetsRepository $betsRepository */
        $betsRepository = Repository::getRepository(BetsRepository::class);

        $betPackages = $betsRepository->getAllPackages();

        foreach ($betPackages as $package) {

            $id = (int)$package['id'];

            if (!$package['probability'] && !$package['ev']) {

                $allBets = array();

                $bets = $betsRepository->geBetsOfPackage($id);

                foreach ($bets as $bet) {

                    if (null == $bet->getEv()) $this->updateBetEv($bet);

                    array_push($allBets, $bet->getResults());
                }

                $p = $cc->calculateProbabilityOfPackage($allBets);

                $betsRepository->updateBetEv($id, null, $p);
            }
        }
    }

    public function updateBetsEvUsingSeparateProcess($totoId)
    {
        /** @var BetsRepository $betsRepository */
        $betsRepository = Repository::getRepository(BetsRepository::class);

        $cc = new CalculationController();

        $betPackages = $betsRepository->getAllPackages();

        foreach ($betPackages as $package) {

            $id = (int)$package['id'];

            if (!$package['probability'] && !$package['ev']) {

                $procArray = [];

                $allBets = [];

                $bets = $betsRepository->geBetsOfPackage($id);

                foreach ($bets as $bet) {

                    $allBets[] = $bet->getResults();

                    if (null == $bet->getEv()) $procArray[] = popen(
                        "php ".ROOT_DIR."/updateBetItemEv.php -t $totoId --id=".$bet->getId()." --type=array &",
                        "w"
                    );
                }

                foreach ($procArray as $proc) {
                    pclose($proc);
                }

                $p = $cc->calculateProbabilityOfPackage($allBets);

                $betsRepository->updateBetEv($id, null, $p);
            }
        }
    }


    public function updateBetItemById($id, $type = 'array')
    {
        /** @var BetsRepository $betsRepository */
        $betsRepository = Repository::getRepository(BetsRepository::class);

        $bet = $betsRepository->getBetItemById($id);

        $this->updateBetEv($bet, $type);
    }

    private function updateBetEv(Bet $bet, $type = 'mysql')
    {
        $cc = new CalculationController();

        /** @var BetsRepository $betsRepository */
        $betsRepository = Repository::getRepository(BetsRepository::class);

        if ($type == 'mysql') {
            list($ev, $p) = $cc->calculateEV($bet->getResults(), $bet->getMoney());
        }
        else {
            list($ev, $p) = $cc->calculateEVUsingArray($bet->getResults(), $bet->getMoney());
        }

        $betsRepository->updateBetItemEv($bet->getId(), $ev, $p);
    }

    public function checkInPool(array $bet)
    {
        /** @var PoolRepository $poloRepository */
        $poloRepository = PoolRepository::getRepository(PoolRepository::class);

        return $poloRepository->getPoolItem($bet);
    }

    public function updateBreakDownsAction()
    {
        $testedPool = [
            [1, 2, 1, 'X', 1, 1, 1, 1, 2, 1, 2, 2, 1, 2, 50],
            [1, 2, 1, 'X', 1, 1, 1, 1, 2, 1, 2, 2, 1, 2, 50],
            [1, 2, 1, 'X', 1, 1, 1, 1, 2, 1, 2, 2, 1, 2, 50],
            [1, 2, 1, 'X', 1, 1, 1, 1, 2, 1, 2, 2, 1, 2, 50]
        ];

        $preparedResultRepository = new PreparedResultRepository();

        /** @var TotoRepository $totoRepository */
        $totoRepository = Repository::getRepository(TotoRepository::class);

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
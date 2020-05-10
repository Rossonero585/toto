<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 15/10/17
 * Time: 21:18
 */
namespace Controllers;

use Builders\Providers\TotoFromWeb;
use Builders\TotoBuilder;
use Helpers\ArrayHelper;
use Helpers\EventsHelper;
use Helpers\TotoHelper;
use Models\Bet;
use Models\BetPackage;
use Models\BreakDown;
use Models\BreakDownItem;
use Models\Event;
use Repositories\BetItemRepository;
use Repositories\BetPackageRepository;
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

        /** @var \PDO $pdo */
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

        $jsonToto = TotoHelper::getJsonToto($totoId);

        $totoEvents = EventsHelper::getEventsFromJson($jsonToto);

        /** @var TotoRepository $totoRepository */
        $totoRepository = Repository::getRepository(TotoRepository::class);

        $toto = TotoBuilder::createToto(new TotoFromWeb($jsonToto));

        $totoRepository->addToto($toto);

        /** @var EventRepository $eventRepository */
        $eventRepository = Repository::getRepository(EventRepository::class);

        foreach ($totoEvents as $event)
        {
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

        /** @var BetPackageRepository $betPackageRepository */
        $betPackageRepository = Repository::getRepository(BetPackageRepository::class);

        /** @var BetItemRepository $betItemsRepository */
        $betItemsRepository = Repository::getRepository(BetItemRepository::class);

        $betPackages = $betPackageRepository->getAllPackages();

        /** @var BetPackage $package */
        foreach ($betPackages as $package) {

            $id = $package->getId();

            if (!$package->getProbability() && !$package->getEv() ) {

                $allBets = array();

                $bets = $betItemsRepository->geBetsOfPackage($id);

                foreach ($bets as $bet) {

                    if (null == $bet->getEv()) $this->updateBetEv($bet);

                    array_push($allBets, $bet->getResults());
                }

                $p = $cc->calculateProbabilityOfPackage($allBets);

                $betPackageRepository->updateBetEv($id, null, $p);
            }
        }
    }

    public function updateBetsEvUsingSeparateProcess($totoId, array $ids = [])
    {
        /** @var BetPackageRepository $betPackageRepository */
        $betPackageRepository = Repository::getRepository(BetPackageRepository::class);

        /** @var BetItemRepository $betItemsRepository */
        $betItemsRepository = Repository::getRepository(BetItemRepository::class);

        $cc = new CalculationController();

        $betPackages = $betPackageRepository->getAllPackages();

        foreach ($betPackages as $package) {

            $id = $package->getId();

            if (!$package->getProbability() && !$package->getEv() ) {

                $procArray = [];

                $allBets = [];

                $bets = $betItemsRepository->geBetsOfPackage($id);

                foreach ($bets as $key => $bet) {

                    if ($ids && in_array($key, $ids)) {

                        $allBets[] = $bet->getResults();

                        if (null == $bet->getEv()) $procArray[] = popen(
                            "php ".ROOT_DIR."/updateBetItemEv.php -t $totoId --id=".$bet->getId()." --type=array &",
                            "w"
                        );
                    }

                }

                foreach ($procArray as $proc) {
                    pclose($proc);
                }

                $p = $cc->calculateProbabilityOfPackage($allBets);

                $betPackageRepository->updateBetEv($id, null, $p);
            }
        }
    }

    public function updateRandomBetsUsingSeveralProcesses($totoId)
    {
        /** @var BetPackageRepository $betPackageRepository */
        $betPackageRepository = Repository::getRepository(BetPackageRepository::class);

        /** @var BetItemRepository $betItemsRepository */
        $betItemsRepository = Repository::getRepository(BetItemRepository::class);

        $betPackages = $betPackageRepository->getAllPackages();

        $max = 0;

        foreach ($betPackages as $package) {

            $bets = $betItemsRepository->geBetsOfPackage($package->getId());

            if (count($bets) > $max) $max = count($bets);
        }

        $ids = [];

        for ($i = 1; $i <= $_ENV['COUNT_BETS_FOR_CALC']; $i++) {
            array_push($ids, rand(0, $max - 1));
        }

        $this->updateBetsEvUsingSeparateProcess($totoId, $ids);
    }


    public function updateBetItemById($id, $type = 'array')
    {
        /** @var BetItemRepository $betItemsRepository */
        $betItemsRepository = Repository::getRepository(BetItemRepository::class);

        $bet = $betItemsRepository->getBetItemById($id);

        $this->updateBetEv($bet, $type);
    }

    private function updateBetEv(Bet $bet, $type = 'mysql')
    {
        $cc = new CalculationController();

        /** @var BetItemRepository $betItemsRepository */
        $betItemsRepository = Repository::getRepository(BetItemRepository::class);

        if ($type == 'mysql') {
            list($ev, $p) = $cc->calculateEV($bet->getResults(), $bet->getMoney());
        }
        else {
            list($ev, $p) = $cc->calculateEVUsingArray($bet->getResults(), $bet->getMoney());
        }

        $betItemsRepository->updateBetItemEv($bet->getId(), $ev, $p);
    }

    public function checkInPool(array $bet)
    {
        /** @var PoolRepository $poloRepository */
        $poloRepository = PoolRepository::getRepository(PoolRepository::class);

        return $poloRepository->getPoolItem($bet);
    }

    public function updateTotoResult(int $totoId)
    {

        /** @var EventRepository $eventRepository */
        $eventRepository = Repository::getRepository(EventRepository::class);

        /** @var TotoRepository $totoRepository */
        $totoRepository = Repository::getRepository(TotoRepository::class);

        /** @var PoolRepository $poolRepository */
        $poolRepository = Repository::getRepository(PoolRepository::class);

        /** @var BetPackageRepository $betPackageRepository */
        $betPackageRepository = Repository::getRepository(BetPackageRepository::class);

        /** @var BetItemRepository $betItemsRepository */
        $betItemsRepository = Repository::getRepository(BetItemRepository::class);

        $totoJson = TotoHelper::getJsonToto($totoId);

        $actualEvents = EventsHelper::getEventsFromJson($totoJson);

        $events = $eventRepository->getAll();

        $eventHelper = new EventsHelper($events);

        $results = [];

        foreach ($actualEvents as $event) {

            $result = $event->isCanceled() ? [1, 'X', 2] : $event->getResult();

            array_push($results, $result);
        }

        /** @var Event $event */
        foreach ($events as $key => $event) {
            $eventRepository->updateEventResultById($event->getId(), $actualEvents[$key]->getResult());
        }

        $totoRepository->updateDeviation($eventHelper->getAverageDeviation());

        $toto = $totoRepository->getToto();

        $breakDown = $poolRepository->getWinnersBreakDown($results);

        $betPackages = $betPackageRepository->getAllPackages();

        foreach ($betPackages as $package) {

            $bets = $betItemsRepository->geBetsOfPackage($package->getId());

            foreach ($bets as $bet) {

                $totoHelper = new TotoHelper($toto, $bet->getMoney());

                $countMatch = ArrayHelper::countMatchResult($bet->getResults(), $results);

                if ($countMatch >= $toto->getMinWinnerCount()) {

                    $ratio = $totoHelper->getRatioByWinCount($countMatch, $breakDown);

                    $income = ($ratio - 1) * $bet->getMoney();
                }
                else {
                    $income = 0;
                }

                $betItemsRepository->updateBetItemIncome($bet->getId(), $countMatch, $income);
            }
        }

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

    public function updateDeviation()
    {
        /** @var TotoRepository $totoRepository */
        $totoRepository = Repository::getRepository(TotoRepository::class);

        /** @var EventRepository $eventsRepository */
        $eventsRepository = Repository::getRepository(EventRepository::class);

        $eventHelper = new EventsHelper($eventsRepository->getAll());

        $totoRepository->updateDeviation($eventHelper->getAverageDeviation());
    }
}
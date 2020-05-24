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
use Helpers\Http\BetCityClient;
use Helpers\TotoHelper;
use Models\Bet;
use Models\BetPackage;
use Models\Event;
use Repositories\BetItemRepository;
use Repositories\BetPackageRepository;
use Repositories\EventRepository;
use Repositories\PoolRepository;
use Repositories\Repository;
use Repositories\TotoRepository;

class UpdateController
{

    const BET_CITY = "https://hdr.betcity.ru";

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


    public function updateBetItemById($id)
    {
        /** @var BetItemRepository $betItemsRepository */
        $betItemsRepository = Repository::getRepository(BetItemRepository::class);

        $bet = $betItemsRepository->getBetItemById($id);

        $this->updateBetEv($bet);
    }

    private function updateBetEv(Bet $bet)
    {
        $cc = new CalculationController();

        /** @var BetItemRepository $betItemsRepository */
        $betItemsRepository = Repository::getRepository(BetItemRepository::class);

        list($ev, $p) = $cc->calculateEV($bet->getResults(), $bet->getMoney());

        $betItemsRepository->updateBetItemEv($bet->getId(), $ev, $p);
    }

    public function checkInPool(array $bet)
    {
        /** @var PoolRepository $poloRepository */
        $poloRepository = PoolRepository::getRepository(PoolRepository::class);

        return $poloRepository->getPoolItem($bet);
    }

    public function updateTotoResult(string $totoId)
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

    public function updateDeviation()
    {
        /** @var TotoRepository $totoRepository */
        $totoRepository = Repository::getRepository(TotoRepository::class);

        /** @var EventRepository $eventsRepository */
        $eventsRepository = Repository::getRepository(EventRepository::class);

        $eventHelper = new EventsHelper($eventsRepository->getAll());

        $totoRepository->updateDeviation($eventHelper->getAverageDeviation());
    }

    public function setBetCityTokens($totoId)
    {
        $betCityClient = new BetCityClient($totoId);

        $betCityClient->setTokens();
    }
}
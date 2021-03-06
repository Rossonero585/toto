<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 21/10/17
 * Time: 16:20
 */

namespace Controllers;

use Helpers\Arguments;
use Helpers\ArrayHelper;
use Helpers\EventsHelper;
use Helpers\PoolHelper;
use Helpers\TotoHelper;
use Repositories\BetItemRepository;
use Repositories\EventRepository;
use Repositories\Repository;
use Repositories\TotoRepository;
use \DateTime;

class CalculationController
{

    public function calculateRatioAction(array $results, int $cat)
    {
        /** @var TotoRepository $totoRepository */
        $totoRepository = Repository::getRepository(TotoRepository::class);

        $toto = $totoRepository->getToto();

        $countResults = count($results);

        $countEvents = $toto->getEventCount();

        if ($countResults != $countEvents) {
            throw new \Exception("Count results - $countResults don't conform the event's count - $countEvents");
        }

        $poolHelper = new PoolHelper();

        $breakDown = $poolHelper->getWinnersBreakDown($results);

        $totoHelper = new TotoHelper($toto, 0);

        $ratio = $totoHelper->getRatioByCategory($cat, $breakDown);

        return $ratio;
    }

    /**
     * @param array $bet
     * @param float $betSize
     * @param bool $includeBet
     * @return array
     * @throws \Exceptions\UnknownRepository
     */
    public function calculateEV(array $bet, float $betSize, $includeBet = true)
    {
        /** @var TotoRepository $totoRepository */
        $totoRepository = Repository::getRepository(TotoRepository::class);

        $totoHelper = new TotoHelper($totoRepository->getToto(), $betSize);

        /** @var EventRepository $eventRepository */
        $eventRepository = Repository::getRepository(EventRepository::class);

        $eventHelper = new EventsHelper($eventRepository->getAll());

        $poolHelper = new PoolHelper();

        $totoId = Arguments::getArguments()->get('t');

        $pWin = 0;

        $m = 0;

        $countAllCombinations = 0;

        foreach ($totoHelper->iterateWinnerCombinations($bet) as $betItem) {

            $countAllCombinations++;

            $p = $eventHelper->calculateProbabilityOfAllEvents($betItem);

            $pWin = $pWin + $p;
        }

        $minus = $betSize * (1 - $pWin);

        $tempCount = 0;


        foreach ($totoHelper->iterateWinnerCombinations($bet) as $betItem) {

            $tempCount++;

            $p = $eventHelper->calculateProbabilityOfAllEvents($betItem);

            $breakDown = $poolHelper->getWinnersBreakDown($betItem, true);

            $countMatch = ArrayHelper::countMatchValues($betItem, $bet);

            $ratio = $totoHelper->getRatioByWinCount($countMatch, $breakDown, $includeBet);

            $m = $m + $p * $ratio * $betSize;

            if ($tempCount % 10000 === 0) {

                $percent = ($tempCount / $countAllCombinations) * 100;

                $betAsString = implode($bet);

                $str = "$totoId;$betAsString;$tempCount/$countAllCombinations;$m/$minus;$percent";

                $this->log($str);
            }

        }

        $m = $m - $betSize*(1 - $pWin);

        return [$m, $pWin];
    }


    public function calculateExpectedRatioForCategory(array $bet, float $betSize, int $cat)
    {
        /** @var TotoRepository $totoRepository */
        $totoRepository = Repository::getRepository(TotoRepository::class);

        $totoHelper = new TotoHelper($totoRepository->getToto(), $betSize);

        /** @var EventRepository $eventRepository */
        $eventRepository = Repository::getRepository(EventRepository::class);

        $eventHelper = new EventsHelper($eventRepository->getAll());

        $poolHelper = new PoolHelper();

        $sumP = 0;

        $sumRatio = 0;

        foreach ($totoHelper->iterateWinnerCombinations($bet) as $betItem) {

            if (ArrayHelper::countMatchValues($bet, $betItem) === $cat) {

                $p = $eventHelper->calculateProbabilityOfAllEvents($betItem);

                $breakDown = $poolHelper->getWinnersBreakDown($betItem);

                $ratio = $totoHelper->getRatioByCategory($cat, $breakDown);

                $sumRatio += $p*$ratio;

                $sumP += $p;
            }
        }

        return [$sumP, $sumRatio / $sumP];

    }

    public function calculateProbabilityOfPackage(array $bets)
    {
        /** @var TotoRepository $totoRepository */
        $totoRepository = Repository::getRepository(TotoRepository::class);

        $totoHelper = new TotoHelper($totoRepository->getToto(), 50);

        /** @var EventRepository $eventRepository */
        $eventRepository = Repository::getRepository(EventRepository::class);

        $eventHelper = new EventsHelper($eventRepository->getAll());

        $map = [];

        $sumP = 0;

        foreach ($bets as $bet) {

            foreach ($totoHelper->iterateWinnerCombinations($bet) as $betItem) {

                $key = implode("", $betItem);

                if (!isset($map[$key])) {

                    $p = $eventHelper->calculateProbabilityOfAllEvents($betItem);

                    $sumP += $p;

                    $map[$key] = 1;
                }
            }
        }

        return $sumP;
    }


    public function calculateProbabilityOfPackageByCategories(int $packageId)
    {
        /** @var BetItemRepository $betItemRepository */
        $betItemRepository = Repository::getRepository(BetItemRepository::class);

        $bets = $betItemRepository->geBetsOfPackage($packageId);

        /** @var TotoRepository $totoRepository */
        $totoRepository = Repository::getRepository(TotoRepository::class);

        $toto = $totoRepository->getToto();

        $totoHelper = new TotoHelper($toto, 50);

        /** @var EventRepository $eventRepository */
        $eventRepository = Repository::getRepository(EventRepository::class);

        $eventHelper = new EventsHelper($eventRepository->getAll());

        $winnerCombinations = array_keys($toto->getWinnerCounts());

        $map = [];

        $probabilities = [];

        foreach ($winnerCombinations as $count) {
            $probabilities[$count] = 0;
        }

        foreach ($bets as $betModel) {

            $bet = $betModel->getResults();

            foreach ($totoHelper->iterateWinnerCombinations($bet) as $betItem) {

                $countMatch = ArrayHelper::countMatchValues($betItem, $bet);

                $p = $eventHelper->calculateProbabilityOfAllEvents($betItem);

                $key = implode("", $betItem);

                if (!isset($map[$key])) {

                    $map[$key] = $countMatch;

                    while(isset($probabilities[$countMatch])) {
                        $probabilities[$countMatch] += $p;
                        $countMatch--;
                    }
                }
                else {
                    if ($map[$key] < $countMatch) {
                        for ($i = $map[$key] + 1; $i <= $countMatch; $i++) {
                            $probabilities[$i] += $p;
                        }
                        $map[$key] = $countMatch;
                    }
                }
            }
        }

        return $probabilities;
    }

    public function calculateEvOfPackage($pathToFile)
    {
        /** @var TotoRepository $totoRepository */
        $totoRepository = Repository::getRepository(TotoRepository::class);

        $toto = $totoRepository->getToto();

        $totoHelper = new TotoHelper($toto, 50);

        /** @var EventRepository $eventRepository */
        $eventRepository = Repository::getRepository(EventRepository::class);

        $poolHelper = new PoolHelper();

        $eventHelper = new EventsHelper($eventRepository->getAll());

        list($bets, $betSize, $money) = $this->getBetsFromFile($pathToFile);

        $commonMap = [];

        $pWin = 0;

        $m = 0;

        foreach ($bets as $bet) {

            $tempMap = [];

            foreach ($totoHelper->iterateWinnerCombinations($bet) as $betItem) {

                $key = implode("", $betItem);

                if (!isset($commonMap[$key])) {

                    $p = $eventHelper->calculateProbabilityOfAllEvents($betItem);

                    $pWin += $p;

                    $commonMap[$key] = $p;
                }

                $count = ArrayHelper::countMatchValues($betItem, $bet);

                $totoHelper = new TotoHelper($toto, $betSize);

                $breakDown = $poolHelper->getWinnersBreakDown($betItem, true);

                $ratio = $totoHelper->getRatioByWinCount($count, $breakDown);

                $m = $m + $commonMap[$key] * $ratio * $betSize;

                $tempMap[$key] = 1;

            }
        }

        $m = $m - $money*(1 - $pWin);

        return [$pWin,$m];

    }

    private function getBetsFromFile($pathToFile)
    {
        $content = file_get_contents($pathToFile);

        $lines = explode(PHP_EOL, $content);

        $betSize = 0;

        $money = 0;

        $bets = array_map(
            function($l) use(&$betSize, &$money) {
                $items = explode(";", $l);
                $betSize = (float)array_shift($items);
                $money += $betSize;
                array_pop($items);
                return array_map(function($item) {
                    return explode("=", $item)[1];
                }, $items);
            },
            $lines
        );

        return [$bets, $betSize, $money];
    }

    private function log($str)
    {
        $str  = (new DateTime())->format(DATE_ISO8601)." - ".$str.PHP_EOL;

        file_put_contents(ROOT_DIR."/perfomance.log", $str, FILE_APPEND);
    }
}
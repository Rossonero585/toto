<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 21/10/17
 * Time: 16:20
 */

namespace Controllers;

use Helpers\ArrayHelper;
use Helpers\EventsHelper;
use Helpers\PoolHelper;
use Helpers\TotoHelper;
use Repositories\EventRepository;
use Repositories\Repository;
use Repositories\TotoRepository;

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

        $cancelledEventIndex = $eventHelper->getIndexOfCanceledEvent();

        $poolHelper = new PoolHelper();

        $pWin = 0;

        $m = 0;

        foreach ($totoHelper->iterateWinnerCombinations($bet) as $betItem) {

            if ($cancelledEventIndex > 0) $betItem[$cancelledEventIndex - 1] = '4';

            $p = $eventHelper->calculateProbabilityOfAllEvents($betItem);

            $breakDown = $poolHelper->getWinnersBreakDown($betItem, true);

            $countMatch = ArrayHelper::countMatchValues($betItem, $bet);

            $ratio = $totoHelper->getRatioByWinCount($countMatch, $breakDown, $includeBet);

            $m = $m + $p * ($ratio - 1) * $betSize;

            $pWin = $pWin + $p;
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

        $cancelledEventIndex = $eventHelper->getIndexOfCanceledEvent();

        $poolHelper = new PoolHelper();

        $sumP = 0;

        $sumRatio = 0;

        foreach ($totoHelper->iterateWinnerCombinations($bet) as $betItem) {

            if (ArrayHelper::countMatchValues($bet, $betItem) === $cat) {

                if ($cancelledEventIndex > 0) $betItem[$cancelledEventIndex - 1] = '4';

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

        $cancelledEventIndex = $eventHelper->getIndexOfCanceledEvent();

        $map = [];

        $sumP = 0;

        foreach ($bets as $bet) {

            foreach ($totoHelper->iterateWinnerCombinations($bet) as $betItem) {

                $key = implode("", $betItem);

                if (!isset($map[$key])) {

                    if ($cancelledEventIndex > 0) $betItem[$cancelledEventIndex - 1] = '4';

                    $p = $eventHelper->calculateProbabilityOfAllEvents($betItem);

                    $sumP += $p;

                    $map[$key] = 1;
                }
            }
        }

        return $sumP;
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

        $cancelledEventIndex = $eventHelper->getIndexOfCanceledEvent();

        list($bets, $betSize, $money) = $this->getBetsFromFile($pathToFile);

        $commonMap = [];

        $pWin = 0;

        $m = 0;

        foreach ($bets as $bet) {

            $tempMap = [];

            foreach ($totoHelper->iterateWinnerCombinations($bet) as $betItem) {

                $key = implode("", $betItem);

                if ($cancelledEventIndex > 0) $betItem[$cancelledEventIndex - 1] = '4';

                if (!isset($commonMap[$key])) {

                    $p = $eventHelper->calculateProbabilityOfAllEvents($betItem);

                    $pWin += $p;

                    $commonMap[$key] = $p;
                }

                $count = ArrayHelper::countMatchValues($betItem, $bet);

                $totoHelper = new TotoHelper($toto, $betSize);

                $breakDown = $poolHelper->getWinnersBreakDown($betItem, true);

                $ratio = $totoHelper->getRatioByWinCount($count, $breakDown);

                $m = $m + $commonMap[$key] * ($ratio - 1) * $betSize;

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
}
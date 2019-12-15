<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 21/10/17
 * Time: 16:20
 */

namespace Controllers;

use drupol\phpermutations\Generators\Combinations;
use Helpers\ArrayHelper;
use Helpers\EventsHelper;
use Helpers\TotoHelper;
use Repositories\EventRepository;
use Repositories\PoolRepository;
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

        /** @var PoolRepository $poolRepository */
        $poolRepository = Repository::getRepository(PoolRepository::class);

        $breakDown = $poolRepository->getWinnersBreakDown($results);

        $totoHelper = new TotoHelper($toto, 50);

        $ratio = $totoHelper->getRatioByCategory($cat, $breakDown);

        return $ratio;
    }

    /**
     * @param array $bet
     * @param float $betSize
     * @return array
     */
    public function calculateEV(array $bet, float $betSize)
    {
        /** @var TotoRepository $totoRepository */
        $totoRepository = Repository::getRepository(TotoRepository::class);

        $totoHelper = new TotoHelper($totoRepository->getToto(), $betSize);

        /** @var EventRepository $eventRepository */
        $eventRepository = Repository::getRepository(EventRepository::class);

        $eventHelper = new EventsHelper($eventRepository->getAll());

        /** @var PoolRepository $poolRepository */
        $poolRepository = Repository::getRepository(PoolRepository::class);

        $toto = $totoRepository->getToto();

        $winnerCounts = array_keys($toto->getWinnerCounts());

        $range = range(1, $toto->getEventCount());

        $pWin = 0;

        $m = 0;

        $map = [];

        $winnerCounts = array_reverse($winnerCounts);

        foreach ($winnerCounts as $count) {

            $combinations = new Combinations($range, $count);

            foreach ($combinations->generator() as $combination) {

                foreach (ArrayHelper::fillCombination($bet, $combination) as $betItem) {

                    $key = implode(",", $betItem);

                    if (!isset($map[$key])) {

                        $betItem = array_map(function ($key, $value) use($eventHelper) {

                            if ($eventHelper->getEvent($key + 1)->isCanceled()) {
                                return [1, 'X', 2];
                            }

                            return $value;

                        }, array_keys($betItem), $betItem);

                        $p = $eventHelper->calculateProbabilityOfAllEvents($betItem);

                        $breakDown = $poolRepository->getWinnersBreakDown($betItem);

                        $ratio = $totoHelper->getRatioByWinCount($count, $breakDown);

                        $m = $m + $p * ($ratio - 1) * $betSize;

                        $pWin = $pWin + $p;

                        $map[$key] = $pWin;
                    }
                }
            }
        }

        $m = $m - $betSize*(1 - $pWin);

        return [$m, $pWin];
    }


    /**
     * @param array $bet
     * @param float $betSize
     * @return array
     */
    public function calculateEVUsingArray(array $bet, float $betSize)
    {
        /** @var TotoRepository $totoRepository */
        $totoRepository = Repository::getRepository(TotoRepository::class);

        $totoHelper = new TotoHelper($totoRepository->getToto(), $betSize);

        /** @var EventRepository $eventRepository */
        $eventRepository = Repository::getRepository(EventRepository::class);

        $eventHelper = new EventsHelper($eventRepository->getAll());

        /** @var PoolRepository $poolRepository */
        $poolRepository = Repository::getRepository(PoolRepository::class);

        $toto = $totoRepository->getToto();

        $winnerCounts = array_keys($toto->getWinnerCounts());

        $range = range(1, $toto->getEventCount());

        $pWin = 0;

        $m = 0;

        $map = [];

        $winnerCounts = array_reverse($winnerCounts);

        foreach ($winnerCounts as $count) {

            $combinations = new Combinations($range, $count);

            foreach ($combinations->generator() as $combination) {

                foreach (ArrayHelper::fillCombination($bet, $combination) as $betItem) {

                    $key = implode(",", $betItem);

                    if (!isset($map[$key])) {

                        $betItem = array_map(function ($key, $value) use($eventHelper) {

                            if ($eventHelper->getEvent($key + 1)->isCanceled()) {
                                return [1, 'X', 2];
                            }

                            return $value;

                        }, array_keys($betItem), $betItem);

                        $p = $eventHelper->calculateProbabilityOfAllEvents($betItem);

                        $breakDown = $poolRepository->getWinnersBreakDownUsingArray($betItem);

                        $ratio = $totoHelper->getRatioByWinCount($count, $breakDown);

                        $m = $m + $p * ($ratio - 1) * $betSize;

                        $pWin = $pWin + $p;

                        $map[$key] = $pWin;
                    }
                }
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

        /** @var PoolRepository $poolRepository */
        $poolRepository = Repository::getRepository(PoolRepository::class);

        $toto = $totoRepository->getToto();

        $range = range(1, $toto->getEventCount());

        $combinations = new Combinations($range, $cat);

        $sumP = 0;

        $sumRatio = 0;

        $map = [];

        /** @var array $item */
        foreach ($combinations->generator() as $comb) {

            foreach (ArrayHelper::fillCombination($bet, $comb) as $betItem) {

                $key = implode("", $betItem);

                if (!isset($map[$key]) && ArrayHelper::countMatchResult($bet, $betItem) == $cat) {

                    $betItem = array_map(function ($key, $value) use($eventHelper) {

                        if ($eventHelper->getEvent($key + 1)->isCanceled()) {
                            return [1, 'X', 2];
                        }

                        return $value;

                    }, array_keys($betItem), $betItem);

                    $p = $eventHelper->calculateProbabilityOfAllEvents($betItem);

                    $breakDown = $poolRepository->getWinnersBreakDown($betItem);

                    $ratio = $totoHelper->getRatioByCategory($cat, $breakDown);

                    $sumRatio += $p*$ratio;

                    $sumP += $p;

                    $map[$key] = 1;
                }

            }
        }

        return [$sumP, $sumRatio / $sumP];

    }

    public function calculateProbabilityOfPackage(array $bets)
    {
        /** @var TotoRepository $totoRepository */
        $totoRepository = Repository::getRepository(TotoRepository::class);

        $toto = $totoRepository->getToto();

        /** @var EventRepository $eventRepository */
        $eventRepository = Repository::getRepository(EventRepository::class);

        $eventHelper = new EventsHelper($eventRepository->getAll());

        $map = [];

        $sumP = 0;

        $winnerCounts = array_keys($toto->getWinnerCounts());

        $winnerCounts = array_reverse($winnerCounts);

        foreach ($bets as $bet) {

            foreach ($winnerCounts as $count) {

                $combinations = new Combinations(range(1, $toto->getEventCount()), $count);

                foreach ($combinations->generator() as $combination) {

                    foreach (ArrayHelper::fillCombination($bet, $combination) as $betItem) {

                        $key = implode("", $betItem);

                        if (!isset($map[$key])) {

                            $p = $eventHelper->calculateProbabilityOfAllEvents($betItem);

                            $sumP += $p;

                            $map[$key] = 1;
                        }

                    }
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

        /** @var EventRepository $eventRepository */
        $eventRepository = Repository::getRepository(EventRepository::class);

        /** @var PoolRepository $poolRepository */
        $poolRepository = Repository::getRepository(PoolRepository::class);


        $eventHelper = new EventsHelper($eventRepository->getAll());

        list($bets, $betSize, $money) = $this->getBetsFromFile($pathToFile);

        $commonMap = [];

        $pWin = 0;

        $m = 0;

        $winnerCounts = array_keys($toto->getWinnerCounts());

        $winnerCounts = array_reverse($winnerCounts);

        foreach ($bets as $bet) {

            $tempMap = [];

            foreach ($winnerCounts as $count) {

                $combinations = new Combinations(range(1, $toto->getEventCount()), $count);

                foreach ($combinations->generator() as $combination) {

                    foreach (ArrayHelper::fillCombination($bet, $combination) as $betItem) {

                        $key = implode("", $betItem);

                        $betItem = array_map(function ($key, $value) use($eventHelper) {

                            if ($eventHelper->getEvent($key + 1)->isCanceled()) {
                                return [1, 'X', 2];
                            }

                            return $value;

                        }, array_keys($betItem), $betItem);


                        if (!isset($commonMap[$key])) {

                            $p = $eventHelper->calculateProbabilityOfAllEvents($betItem);

                            $pWin += $p;

                            $commonMap[$key] = $p;
                        }

                        if (!isset($tempMap[$key])) {

                            $totoHelper = new TotoHelper($toto, $betSize);

                            $breakDown = $poolRepository->getWinnersBreakDown($betItem);

                            $ratio = $totoHelper->getRatioByWinCount($count, $breakDown);

                            $m = $m + $commonMap[$key] * ($ratio - 1) * $betSize;

                            $tempMap[$key] = 1;
                        }

                    }
                }
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

    private function writeLog(string $file, $line)
    {
        $path = ROOT_DIR.DIRECTORY_SEPARATOR."stats".DIRECTORY_SEPARATOR.$file;

        file_put_contents($path, $line.PHP_EOL, FILE_APPEND);
    }
}
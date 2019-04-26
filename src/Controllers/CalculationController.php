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
use Models\Event;
use Repositories\EventRepository;
use Repositories\PoolRepository;
use Repositories\PreparedResultRepository;
use Repositories\TotoRepository;

class CalculationController
{
    /** @var  TotoRepository */
    private $totoRepository;

    /** @var  PoolRepository */
    private $poolRepository;

    /** @var  EventRepository */
    private $eventsRepository;


    private function getTotoRepository()
    {
        if (!$this->totoRepository) {
            $this->totoRepository = new TotoRepository();
        }

        return $this->totoRepository;
    }

    private function getPoolRepository()
    {
        if (!$this->poolRepository) {
            $this->poolRepository = new PoolRepository();
        }

        return $this->poolRepository;
    }

    private function getEventsRepository()
    {
        if (!$this->eventsRepository) {
            $this->eventsRepository = new EventRepository();
        }

        return $this->eventsRepository;
    }


    public function calculateRatioAction(array $results)
    {
        $totoRepository = new TotoRepository();

        $toto = $totoRepository->getToto();

        $countResults = count($results);

        $countEvents = $toto->getEventCount();

        if ($countResults != $countEvents) {
            throw new \Exception("Count results - $countResults don't conform the event's count - $countEvents");
        }

        $poolRepository = new PoolRepository();

        $breakDown = $poolRepository->getWinnersBreakDown($results);

        $totoHelper = new TotoHelper($toto, 58.12);

        $ratio = $totoHelper->getRatioByWinCount(10, $breakDown);

        return $ratio;
    }

    /**
     * @param array $bet
     * @param float $betSize
     * @return array
     */
    public function calculateEV(array $bet, float $betSize)
    {
        $totoRepository = $this->getTotoRepository();

        $totoHelper = new TotoHelper($totoRepository->getToto(), $betSize);

        $eventRepository = $this->getEventsRepository();

        $eventHelper = new EventsHelper($eventRepository->getAll());

        $poolRepository = $this->getPoolRepository();

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

}
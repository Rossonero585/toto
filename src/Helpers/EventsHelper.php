<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 22/10/17
 * Time: 11:42
 */

namespace Helpers;

use Models\Event;

class EventsHelper
{
    /** @var  Event[] */
    private $events = [];

    public function __construct(array $events)
    {
        /** @var Event $event */
        foreach ($events as $event) {
            $this->events[$event->getId()] = $event;
        }
    }

    /**
     * @param array $bet
     * @param array $win
     * @return float|int
     * @throws \Exception
     */
    public function calculateProbabilityOfCombination(array $bet, array $win)
    {
        $countEvents = count($this->events);

        if ($countEvents != count($bet)) {
            throw new \Exception("Bet array doesn't conform events count");
        }

        if (count($win) > $countEvents) {
            throw new \Exception("Results array doesn't conform events count");
        }

        $p = 1;

        foreach ($bet as $key => $item) {

            $eventId = $key + 1;

            $success = in_array($eventId, $win);

            $p = $p * $this->getProbabilityForResult($eventId, [$item], $success);
        }

        return $p;
    }

    public function calculateProbabilityOfAllEvents(array $bet)
    {
        $p = 1;

        foreach ($bet as $key => $item) {

            $eventId = $key + 1;

            $p = $p * $this->getProbabilityForResult($eventId, [$item]);
        }

        return $p;
    }

    /**
     * @param int $eventId
     * @param array $result
     * @param bool $success
     * @return float|int
     * @throws \Exception
     */
    public function getProbabilityForResult($eventId, $result, $success = true)
    {
        $event = $this->events[$eventId];

        if (!$event) {
            throw new \Exception("Unknown event with id $eventId");
        }

        if ($event->isCanceled()) {
            return 1;
        }

        $p = 0;

        if (in_array(1, $result))   $p = $p + $event->getP1();
        if (in_array('x', $result) || in_array('X', $result)) $p = $p + $event->getPx();
        if (in_array(2, $result))   $p = $p + $event->getP2();

        return $success ? $p : 1 - $p;

    }


    /**
     * @param $id
     * @return Event
     */
    public function getEvent($id)
    {
        return $this->events[$id];
    }

    /**
     * @return float|int
     */
    public function getAverageDeviation()
    {
        $deviation = 0;

        $count = 0;

        /** @var Event $event */
        foreach ($this->events as $event) {

            if (
                $event->getP1() && $event->getPx() && $event->getP2()
                && $event->getS1() && $event->getSx() && $event->getS2()
            ) {

                if ($event->getP1() > $event->getS1()) $deviation += $event->getP1() * ($event->getP1() - $event->getS1());
                if ($event->getP2() > $event->getS2()) $deviation += $event->getP2() * ($event->getP2() - $event->getS2());
                if ($event->getPx() > $event->getSx()) $deviation += $event->getPx() * ($event->getPx() - $event->getSx());

                $count++;
            }

        }

        return $deviation / $count;
    }
}

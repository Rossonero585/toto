<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 22/10/17
 * Time: 11:42
 */

namespace Helpers;

use Models\Event;
use \Exception;

class EventsHelper
{
    /** @var  Event[] */
    private $events = [];

    public function __construct(array $events)
    {
        /** @var Event $event */
        foreach ($events as $event) {
            $this->events[$event->getNumber()] = $event;
        }
    }


    public function calculateProbabilityOfAllEvents(array $bet)
    {
        $p = 1;

        foreach ($bet as $key => $item) {

            $eventId = $key + 1;

            $p = $p * $this->getProbabilityForResult($eventId, $item);
        }

        return $p;
    }

    /**
     * @param int $eventId
     * @param string $result
     * @return float|int
     * @throws \Exception
     */
    public function getProbabilityForResult($eventId, $result)
    {
        $event = $this->events[$eventId];

        if (!$event) {
            throw new Exception("Unknown event with id $eventId");
        }

        if ($event->isCanceled()) {
            return 1;
        }

        if ('1' === $result) {
            return $event->getP1();
        }
        elseif ('2' === $result) {
            return  $event->getP2();
        }
        elseif ('X' === $result || 'x' === $result) {
            return $event->getPx();
        }
        elseif ('4' === $result) {
            return 1;
        }

        throw new Exception("Unknown result $result");
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


    public function getIndexOfCanceledEvent()
    {
        foreach ($this->events as $event) {
            if ($event->isCanceled()) return $event->getNumber();
        }

        return -1;
    }
}

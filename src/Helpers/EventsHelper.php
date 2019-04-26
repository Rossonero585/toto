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

            $p = $p * $this->getProbabilityForResult($eventId, $item, $success);
        }

        return $p;
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
     * @param mixed $result
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

        switch ($result) {
            case '1':
                return $success ? $event->getP1() : 1 - $event->getP1();
                break;
            case 'x':
                return $success ? $event->getPx() : 1 - $event->getPx();
                break;
            case '2':
                return $success ? $event->getP2() : 1 - $event->getP2();
            default:
                throw new \Exception("Incorrect result $result");
        }
    }


}
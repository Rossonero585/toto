<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 01/03/20
 * Time: 20:35
 */

namespace Builders\Providers;

use Helpers\BetGenerator;
use Helpers\EventsHelper;
use Models\Event;
use Models\Input\Bet;

class BetRequestFromGenerator extends BetRequest implements BetRequestInterface
{
    /**
     * @var array
     */
    private $bets;

    /**
     * @var Event[]
     */
    private $events;

    /**
     * BetRequestFromTotoDecision constructor.
     * @param string $totoId
     * @param string $bookMaker
     * @param string $eventsFile
     * @param array $bets
     * @param bool $isTest
     */
    public function __construct(string $totoId, string $bookMaker, string $eventsFile, bool $isTest)
    {
        parent::__construct($totoId, $bookMaker, $isTest);

        $this->events = $this->getEventsArray($eventsFile);
    }

    public function getTotoId() : string
    {
        return $this->totoId;
    }

    public function getBets() : array
    {
        if (!$this->bets) {

            $eventHelper = new EventsHelper($this->events);

            $betGenerator = new BetGenerator($eventHelper);

            $this->bets = $this->getBetsArray($betGenerator->generateBets());
        }

        return $this->bets;
    }

    public function getEvents() : array
    {
        return $this->events;
    }

    public function isTest() : bool
    {
        return (bool)$this->isTest;
    }

    private function getBetsArray(array $bets)
    {
        return array_map(function (array $arr) {
            return new Bet(
                50,
                $arr
            );
        }, $bets);
    }

}
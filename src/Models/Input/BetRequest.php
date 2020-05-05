<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 01/03/20
 * Time: 18:30
 */

namespace Models\Input;

use Models\Event;

class BetRequest
{
    /**
     * @var int
     */
    private $totoId;

    /**
     * @var Bet[]
     */
    private $bets;

    /**
     * @var Event[]
     */
    private $events;

    /**
     * @var bool
     */
    private $isTest;

    /**
     * BetRequest constructor.
     * @param int $totoId
     * @param Bet[] $bets
     * @param Event[] $events
     * @param bool $isTest
     */
    public function __construct(int $totoId, array $bets, array $events, bool $isTest)
    {
        $this->totoId = $totoId;
        $this->bets = $bets;
        $this->events = $events;
        $this->isTest = $isTest;
    }

    /**
     * @return int
     */
    public function getTotoId(): int
    {
        return $this->totoId;
    }

    /**
     * @return array
     */
    public function getBets(): array
    {
        return $this->bets;
    }

    /**
     * @return Event[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * @return bool
     */
    public function isTest(): bool
    {
        return $this->isTest;
    }


}
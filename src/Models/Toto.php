<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 06/10/17
 * Time: 21:52
 */

namespace Models;

class Toto
{
    /** @var  \DateTime */
    private $startTime;

    /** @var  float */
    private $pot;

    /** @var  float */
    private $jackPot;

    /** @var  int */
    private $eventCount;

    /** @var  array */
    private $winnerCounts;

    /**
     * Toto constructor.
     * @param \DateTime $startTime
     * @param float $pot
     * @param float $jackPot
     * @param int $count
     * @param array $winnerCounts
     */
    public function __construct(\DateTime $startTime, $pot, $jackPot, $count, array $winnerCounts)
    {
        $this->startTime = $startTime;
        $this->pot = $pot;
        $this->jackPot = $jackPot;
        $this->eventCount = $count;
        $this->winnerCounts = $winnerCounts;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime(): \DateTime
    {
        return $this->startTime;
    }

    /**
     * @return float
     */
    public function getPot(): float
    {
        return $this->pot;
    }

    /**
     * @return float
     */
    public function getJackPot(): float
    {
        return $this->jackPot;
    }

    /**
     * @return int
     */
    public function getEventCount(): int
    {
        return $this->eventCount;
    }

    /**
     * @return array
     */
    public function getWinnerCounts(): array
    {
        return $this->winnerCounts;
    }

    /**
     * @return int
     */
    public function getMinWinnerCount(): int
    {
        return min(array_keys($this->winnerCounts));
    }


}
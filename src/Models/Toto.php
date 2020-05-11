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

    /** @var string */
    private $bookMaker;

    /**
     * Toto constructor.
     * @param \DateTime $startTime
     * @param float $pot
     * @param float $jackPot
     * @param int $count
     * @param array $winnerCounts
     * @param string $bookMaker
     */
    public function __construct(\DateTime $startTime, $pot, $jackPot, $count, array $winnerCounts, string $bookMaker)
    {
        $this->startTime = $startTime;
        $this->pot = $pot;
        $this->jackPot = $jackPot;
        $this->eventCount = $count;
        $this->winnerCounts = $winnerCounts;
        $this->bookMaker = $bookMaker;
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

    /**
     * @return string
     */
    public function getBookMaker(): string
    {
        return $this->bookMaker;
    }

    /**
     * @param string $bookMaker
     */
    public function setBookMaker(string $bookMaker): void
    {
        $this->bookMaker = $bookMaker;
    }

}
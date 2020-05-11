<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 14/10/17
 * Time: 21:37
 */
namespace Builders\Providers;

class TotoFromArray implements TotoInterface
{
    private $row;

    public function __construct(array $row)
    {
        $this->row = $row;
    }

    public function getPot(): float
    {
        return $this->row['pot'];
    }

    public function getJackPot(): float
    {
        return $this->row['jackpot'];
    }

    public function getDateTime(): \DateTime
    {
        return \DateTime::createFromFormat('Y-m-d H:i:s', $this->row['start_date']);
    }

    public function getEventCount(): int
    {
        return $this->row['event_count'];
    }

    public function getWinnerCounts(): array
    {
        return unserialize($this->row['winner_counts']);
    }

    public function getBookMaker(): string
    {
        return $this->row['bookmaker'];
    }


}
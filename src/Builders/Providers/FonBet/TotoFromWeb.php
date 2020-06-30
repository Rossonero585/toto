<?php

namespace Builders\Providers\FonBet;

use Builders\Providers\TotoInterface;

class TotoFromWeb implements TotoInterface
{
    private $toto;

    public function __construct(\stdClass $json)
    {
        $this->toto = $json->d;
    }

    public function getPot(): float
    {
        return $this->toto->Pool;
    }

    public function getJackPot(): float
    {
        return $this->toto->Jackpot;
    }

    public function getDateTime(): \DateTime
    {
        $dateString = $this->toto->Expired;

        $matches = [];

        if (preg_match('/Date\((\d+)\)/', $dateString, $matches) === false) {
            throw new \Exception('Can\'t recognize start date');
        }

        $timeStamp = $matches[1];

        $dateTime = \DateTime::createFromFormat('U', $timeStamp / 1000, new \DateTimeZone('UTC'));

        $dateTime->setTimezone(new \DateTimeZone('Europe/Moscow'));

        return $dateTime;
    }

    public function getEventCount(): int
    {
        return 15;
    }

    public function getWinnerCounts(): array
    {
        return [9 => 0.32, 10 => 0.18, 11 => 0.1, 12 => 0.1, 13 => 0.1, 14 => 0.1, 15 => 0.1];
    }

    public function getBookMaker(): string
    {
        return 'fonbet';
    }

}
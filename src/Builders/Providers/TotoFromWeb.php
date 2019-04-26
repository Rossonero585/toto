<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 14/10/17
 * Time: 22:07
 */

namespace Builders\Providers;

class TotoFromWeb implements TotoInterface
{
    private $toto;

    private $itemsCount;

    public function __construct(\stdClass $toto)
    {
        $this->toto = $toto->reply->toto;
        $this->itemsCount = count($toto->reply->toto->out);
    }

    public function getPot(): float
    {
        return $this->toto->pool;
    }

    public function getJackPot(): float
    {
        return $this->toto->jackpot;
    }

    public function getDateTime(): \DateTime
    {
        return \DateTime::createFromFormat('Y-m-d H:i:s', $this->toto->dt_tt);
    }

    public function getEventCount(): int
    {
        return $this->itemsCount;
    }

    public function getWinnerCounts(): array
    {
        return [9 => 0.3, 10 => 0.2, 11 => 0.15, 12 => 0.1, 13 => 0.1, 14 => 0.05];
    }


}
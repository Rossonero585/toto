<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 04/01/20
 * Time: 23:12
 */

namespace Builders\Providers;

class TotoFromPrevJson implements TotoInterface
{
    /** @var  \stdClass */
    private $obj;

    public function __construct(\stdClass $obj)
    {
        $this->obj = $obj;
    }

    public function getPot(): float
    {
        return $this->obj->pool;
    }

    public function getJackPot(): float
    {
        return $this->obj->jackpot;
    }

    public function getDateTime(): \DateTime
    {
        return \DateTime::createFromFormat(
            "Y-m-d H:i:s",
            $this->obj->dt_tt,
            new \DateTimeZone('Europe/Moscow')
        );
    }

    public function getEventCount(): int
    {
        return 14;
    }

    public function getWinnerCounts(): array
    {
        return [9 => 0.3, 10 => 0.2, 11 => 0.15, 12 => 0.1, 13 => 0.1, 14 => 0.05];
    }

    public function getBookMaker(): string
    {
        return 'betcity';
    }


}
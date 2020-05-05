<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 01/03/20
 * Time: 20:31
 */

namespace Builders\Providers;

use Models\Event;
use Models\Bet;

interface BetRequestInterface
{
    public function getTotoId() : int ;

    /**
     * @return Bet[]
     */
    public function getBets() : array;

    /**
     * @return Event[]
     */
    public function getEvents() : array;

    public function isTest() : bool;
}
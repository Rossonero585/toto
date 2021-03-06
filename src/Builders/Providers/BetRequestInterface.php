<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 01/03/20
 * Time: 20:31
 */

namespace Builders\Providers;

use Models\Event;
use Models\Input\Bet;

interface BetRequestInterface
{
    public function getTotoId() : string ;

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
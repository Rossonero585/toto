<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 14/10/17
 * Time: 21:38
 */

namespace Builders\Providers;
use \DateTime;

interface TotoInterface
{
    public function getTotoId() : string ;

    public function getPot() : float;

    public function getJackPot() : float;

    public function getDateTime() : DateTime;

    public function getEventCount() : int;

    public function getWinnerCounts() : array;

    public function getBookMaker() : string;
}
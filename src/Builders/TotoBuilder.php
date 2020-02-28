<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 14/10/17
 * Time: 22:23
 */

namespace Builders;

use Builders\Providers\TotoInterface;
use Models\Toto;

class TotoBuilder
{
    public static function createToto(TotoInterface $totoProvider)
    {
        return new Toto(
            $totoProvider->getDateTime(),
            $totoProvider->getPot(),
            $totoProvider->getJackPot(),
            $totoProvider->getEventCount(),
            $totoProvider->getWinnerCounts()
        );
    }

    public static function createTotoFromJson()
    {

    }
}
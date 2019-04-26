<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 08/10/17
 * Time: 18:29
 */

namespace Builders;

use Builders\Providers\EventInterface;
use Models\Event;

class EventBuilder
{
    public static function createEvent(EventInterface $eventProvider)
    {
        return new Event(
            $eventProvider->getId(),
            $eventProvider->getP1(),
            $eventProvider->getPx(),
            $eventProvider->getP2(),
            $eventProvider->getS1(),
            $eventProvider->getSx(),
            $eventProvider->getS2(),
            $eventProvider->getLeague(),
            $eventProvider->getTile()
        );
    }


}
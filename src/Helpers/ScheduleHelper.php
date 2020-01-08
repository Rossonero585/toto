<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 06/01/20
 * Time: 12:08
 */

namespace Helpers;

use Models\Toto;

class ScheduleHelper
{
    const TIME_BEFORE_ACTIVATE = 12;

    public function getTimeForRun(Toto $toto)
    {
        $totoStart = $toto->getStartTime();

        $totoStart->setTimezone(new \DateTimeZone('UTC'));

        $timeToToto = $this->getTimeToTotoInSeconds($totoStart);

        if ($timeToToto < self::TIME_BEFORE_ACTIVATE * 60) {
            return $this->getTimeToRunInMinutes($toto->getPot());
        }

        return -1;
    }

    private function getTimeToTotoInSeconds(\DateTime $dateTime)
    {
        $current = new \DateTime('now', new \DateTimeZone('UTC'));

        return $dateTime->getTimestamp() - $current->getTimestamp();
    }

    private function getTimeToRunInMinutes(float $pot)
    {
        if ($pot < 500000) {
            return 4;
        }
        else if ($pot >= 500000 && $pot < 700000) {
            return 5;
        }
        else if ($pot >= 700000 && $pot < 900000) {
            return 7;
        }
        else {
            return 8;
        }
    }


}
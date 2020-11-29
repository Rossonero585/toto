<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 06/01/20
 * Time: 12:08
 */

namespace Helpers;

use Models\Toto;
use \DateTime;
use \DateTimeZone;

class ScheduleHelper
{

    public function getTimeForRun(Toto $toto)
    {
        $totoStart = $toto->getStartTime();

        $totoStart->setTimezone(new DateTimeZone('UTC'));

        $timeToToto = $this->getTimeToTotoInSeconds($totoStart);

        if ($timeToToto < $_ENV['TIME_BEFORE_RUN'] * 60) {
            return $this->getTimeToRunInMinutes($toto->getPot());
        }

        return -1;
    }

    private function getTimeToTotoInSeconds(DateTime $dateTime)
    {
        $current = new DateTime('now', new DateTimeZone('UTC'));

        return $dateTime->getTimestamp() - $current->getTimestamp();
    }

    private function getTimeToRunInMinutes(float $pot)
    {
        if ($pot < 500000) {
            return 1;
        }
        else if ($pot >= 500000 && $pot < 700000) {
            return 1;
        }
        else if ($pot >= 700000 && $pot < 900000) {
            return 1;
        }
        else {
            return 1;
        }
    }


}
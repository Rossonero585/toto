<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 04/01/20
 * Time: 22:46
 */
namespace Controllers;

use Builders\Providers\Factory\NextTotoProviderFactory;
use Helpers\ScheduleHelper;
use Models\Toto;

class CheckController
{

    const CACHE_FILE = "last_bet_toto.txt";

    public function scheduleToto()
    {
        ini_set("display_errors", 0);

        set_time_limit(40);

        $nextTotoProviderFactory = new NextTotoProviderFactory();

        $nextTotoProvider = $nextTotoProviderFactory::getNextTotoProvider('fonbet');

        $nextToto = $nextTotoProvider->getToto();

        if ($nextToto->getId() == $this->getLastBetTotoId($nextToto->getBookMaker())) {
            return;
        }

        $scheduleHelper = new ScheduleHelper();

        $remainMinutes = $scheduleHelper->getTimeForRun($nextToto);

        if ($remainMinutes !== -1) {
            $this->updateLastBetTotoId($nextToto->getBookMaker(), $nextToto->getId());
            $this->printOutput($nextToto, $remainMinutes);
        }
    }

    private function printOutput(Toto $toto, int $remainMinutes)
    {
        $this->updateLastBetTotoId($toto->getBookMaker(), $toto->getId());

        $startTime = $toto->getStartTime();

        $startTime->setTimezone(new \DateTimeZone('UTC'));

        $cloneStartTime = clone $startTime;

        $startTime->modify("-$remainMinutes minutes");

        $timeToRunToto = $startTime->format('H:i');

        $startTime->modify("-3 minutes");

        if ($this->compareMinutes($startTime, $this->getCurrentDateTime())) {
            $timeToRunScript = $startTime->format('H:i');
        }
        else {
            $startTime = $this->getCurrentDateTime();

            $startTime->modify("+1 minutes");

            $timeToRunScript = $startTime->format("H:i");

            $startTime->modify("+3 minutes");

            $timeToRunToto = $startTime->format("H:i");
        }

        $cloneStartTime->modify("+5 minutes");

        $totoStartTime = $cloneStartTime->format("H:i");

        $totoId = $toto->getId();

        list($totoNumber, $bookMaker) = explode("_", $totoId);

        echo "$totoStartTime $timeToRunScript $timeToRunToto $bookMaker $totoNumber $totoId";
    }

    private function getLastBetTotoId(string $bookMaker)
    {
        return (int)file_get_contents($bookMaker."_".self::CACHE_FILE);
    }

    private function updateLastBetTotoId(string $bookMaker, string $totoId)
    {
        file_put_contents($bookMaker."_".self::CACHE_FILE, $totoId);
    }

    private function getCurrentDateTime()
    {
        return new \DateTime('now', new \DateTimeZone('UTC'));
    }

    private function compareMinutes(\DateTime $dateTime1, \DateTime $dateTime2)
    {
        $h1 = (int)$dateTime1->format('H');
        $m1 = (int)$dateTime1->format('i');

        $h2 = (int)$dateTime2->format('H');
        $m2 = (int)$dateTime2->format('i');

        if ($h1 > $h2) {
            return true;
        }
        else if ($m1 > $m2) {
            return true;
        }

        return false;
    }
}
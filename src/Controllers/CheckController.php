<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 04/01/20
 * Time: 22:46
 */
namespace Controllers;

use Builders\Providers\TotoFromPrevJson;
use Builders\TotoBuilder;
use Helpers\ScheduleHelper;

class CheckController
{

    const CACHE_FILE = "last_bet_toto.txt";

    public function scheduleToto()
    {
        ini_set("display_errors", 0);

        set_time_limit(40);

        $betcityUrl = $_ENV['BET_CITY_URL'];

        $content = file_get_contents($betcityUrl."/d/supex/list/cur?page=1&rev=2&ver=54&csn=ooca9s");

        $obj = json_decode($content);

        $toto = null;

        foreach ($obj->reply->totos as $totoObj) {

            $name = $totoObj->name_tt;

            if (mb_strpos($name,"утбол") !== false) {

                $totoId = $totoObj->id_tt;

                $toto = TotoBuilder::createToto(new TotoFromPrevJson($totoObj));

                preg_match('|Суперэкспресс ЕвроФутбол \(№(\d+)\)|', $name, $matches);

                $totoNumber = $matches[1];

                if ($totoId == $this->getLastBetTotoId()) {
                    continue 1;
                }

                $scheduleHelper = new ScheduleHelper();

                $remainMinutes = $scheduleHelper->getTimeForRun($toto);

                if ($remainMinutes !== -1) {

                    $this->updateLastBetTotoId($totoId);

                    $startTime = $toto->getStartTime();

                    $startTime->setTimezone(new \DateTimeZone('UTC'));

                    $cloneStartTime = clone $startTime;

                    $startTime->modify("-$remainMinutes minutes");

                    $timeToRunToto = $startTime->format('H:i');

                    $startTime->modify("-2 minutes");

                    if ($this->compareMinutes($startTime, $this->getCurrentDateTime())) {
                        $timeToRunScript = $startTime->format('H:i');
                    }
                    else {
                        $startTime = $this->getCurrentDateTime();

                        $startTime->modify("+1 minutes");

                        $timeToRunScript = $startTime->format("H:i");

                        $startTime->modify("+2 minutes");

                        $timeToRunToto = $startTime->format("H:i");
                    }

                    $cloneStartTime->modify("+2 minutes");

                    $totoStartTime = $cloneStartTime->format("H:i");

                    echo "$totoStartTime $timeToRunScript $timeToRunToto $totoNumber $totoId"."_betcity";

                    break;
                }
            }
        }
    }

    private function getLastBetTotoId()
    {
        return (int)file_get_contents(self::CACHE_FILE);
    }

    private function updateLastBetTotoId(string $totoId)
    {
        file_put_contents(self::CACHE_FILE, $totoId);
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
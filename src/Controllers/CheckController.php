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

    public function scheduleToto()
    {
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

                $scheduleHelper = new ScheduleHelper();

                $remainMinutes = $scheduleHelper->getTimeForRun($toto);

                if ($remainMinutes !== -1) {

                    $startTime = $toto->getStartTime();

                    $startTime->setTimezone(new \DateTimeZone('UTC'));

                    $startTime->modify("-$remainMinutes minutes");

                    $timeToRunToto = $startTime->format('H:i');

                    $startTime->modify("-2 minutes");

                    $timeToRunScript = $startTime->format('H:i');

                    echo "$timeToRunScript $timeToRunToto $totoNumber $totoId";
                }
            }
        }
    }
}
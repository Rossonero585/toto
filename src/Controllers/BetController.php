<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 01/03/20
 * Time: 20:30
 */

namespace Controllers;

use Builders\BetBuilder;
use Builders\Providers\BetRequestFromPost;
use Builders\Providers\TotoFromWeb;
use Builders\TotoBuilder;
use Helpers\EventsHelper;
use Helpers\FileParser;
use Helpers\Logger;
use Helpers\TotoHelper;
use Models\BetRequest;
use Models\Event;
use phpDocumentor\Reflection\File;
use Repositories\BetsRepository;
use Repositories\EventRepository;
use Repositories\Repository;
use Repositories\TotoRepository;

class BetController
{
    const MIN_DEVIATION = 0.0205;

    public function makeBet()
    {
        $betBuilder = new BetBuilder();

        $betRequest = $betBuilder->createBetRequest(new BetRequestFromPost());

        $this->logBet($betRequest);

        if (!$this->checkEvents($betRequest)) return false;



    }


    private function checkEvents(BetRequest $betRequest)
    {
        $logger = Logger::getInstance();

        $eventsAssoc = FileParser::parseFileWithEvents($betRequest->getEventsFile());

        $totoJson = TotoHelper::getJsonToto($betRequest->getTotoId());

        $events = EventsHelper::getEventsFromMixedProvider($totoJson, $eventsAssoc);

        $eventHelper = new EventsHelper($events);

        if ($deviation = $eventHelper->getAverageDeviation() < self::MIN_DEVIATION) {

            $logger->log("bet", "Refuse to make bet", "Deviation is not acceptable $deviation");

            return false;

        }

        /** @var Event $event */
        foreach ($events as $event) {

            if ($event->isCanceled()) {

                $id = $event->getId();

                $logger->log("bet", "Refuse to make bet", "Event $id is canceled");

                return false;
            }

        }

        return true;
    }



    private function logBet(BetRequest $betRequest)
    {
        $pathToLogs = ROOT_DIR."/log/bet_requests/".$betRequest->getTotoId();

        if (is_dir($pathToLogs)) rmdir($pathToLogs);

        mkdir($pathToLogs,0777, true);

        file_put_contents($pathToLogs."/events.txt", $betRequest->getEventsFile());

        file_put_contents($pathToLogs."/bets.txt", $betRequest->getBetsFile());
    }

}
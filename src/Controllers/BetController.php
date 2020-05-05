<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 01/03/20
 * Time: 20:30
 */

namespace Controllers;

use Builders\BetRequestBuilder;
use Builders\Providers\BetRequestFromTotoDecision;
use Helpers\EventsHelper;
use Helpers\Logger;
use Models\Input\BetRequest;
use Models\Event;

class BetController
{
    const MIN_DEVIATION = 0.0205;

    public function makeBet()
    {
        $betBuilder = new BetRequestBuilder();

        $betRequest = $betBuilder->createBetRequest(new BetRequestFromTotoDecision(
            $_POST['totoId'],
            $this->getBetsContent(),
            $this->getEventsContent(),
            $_POST['isTest']
        ));

        $this->logBet($betRequest);

        if (!$this->checkEvents($betRequest)) return false;

    }


    private function checkEvents(BetRequest $betRequest)
    {
        $logger = Logger::getInstance();

        $events = $betRequest->getEvents();

        $eventHelper = new EventsHelper($events);

        if ($deviation = $eventHelper->getAverageDeviation() < self::MIN_DEVIATION) {

            $logger->log("bet", "Refuse to make bet", "Deviation is not acceptable $deviation");

            return false;
        }

        /** @var Event $event */
        foreach ($events as $event) {

            $id = $event->getId();

            if ($event->isCanceled()) {
                $logger->log("bet", "Refuse to make bet", "Event $id is canceled");
                return false;
            }

            if (!$event->isPinnacle()) {
                $logger->log("bet", "Refuse to make bet", "Event $id is taken not from pinnacle");
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

        file_put_contents($pathToLogs."/events.txt", $this->getEventsContent());

        file_put_contents($pathToLogs."/bets.txt", $this->getBetsContent());
    }

    private function getEventsContent()
    {
        return file_get_contents($_FILES['events_file']);
    }

    private function getBetsContent()
    {
        return file_get_contents($_FILES['bets_file']);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 01/03/20
 * Time: 20:30
 */

namespace Controllers\Http;

use Builders\BetRequestBuilder;
use Builders\Providers\BetRequestFromTotoDecision;
use Helpers\BetRequestHelper;
use Helpers\EventsHelper;
use Helpers\Http\BetCityClient;
use Helpers\Logger;
use Models\Input\BetRequest;
use Models\Event;

class BetController extends Controller
{
    const MIN_DEVIATION = 0.0205;

    public function makeBet() : void
    {
        $logger = Logger::getInstance();

        $betBuilder = new BetRequestBuilder();

        list($totoId, $book) = explode("_", $_REQUEST['toto_id']);

        $betRequest = $betBuilder->createBetRequest(new BetRequestFromTotoDecision(
            $totoId,
            $this->getBetsContent(),
            $this->getEventsContent(),
            (bool)$_REQUEST['is_test']
        ));

        $this->logBet($betRequest);

        if (!$this->checkEvents($betRequest)) {

            $this->sendRequest(400, 'do not pass conditions');

            return;
        }

        $betCityClient = new BetCityClient($betRequest->getTotoId());

        try {
            $betCityClient->makeBet($betRequest->getBets(), $betRequest->isTest());
        }
        catch (\Throwable $exception) {
            $logger->log('main', 'Bet was not successful', $exception->getMessage());
            $this->sendRequest(500, $exception->getMessage());
        }

        $betRequestHelper = new BetRequestHelper();

        try {
            $betRequestHelper->saveBetRequest($betRequest);
        }
        catch (\Throwable $exception) {
            $logger->log(
                'main',
                'Can\'t save bet request into database',
                $exception->getMessage()
            );
            $this->sendRequest(500, $exception->getMessage());
        }

        $this->sendRequest(200, 'success');
    }


    private function checkEvents(BetRequest $betRequest)
    {
        $logger = Logger::getInstance();

        $events = $betRequest->getEvents();

        $eventHelper = new EventsHelper($events);

        if (($deviation = $eventHelper->getAverageDeviation()) < self::MIN_DEVIATION) {

            $logger->log("bet", "Refuse to make bet", "Deviation is not acceptable $deviation");

            return false;
        }

        /** @var Event $event */
        foreach ($events as $event) {

            $id = $event->getNumber();

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

        if (!is_dir($pathToLogs)) mkdir($pathToLogs,0777, true);

        file_put_contents($pathToLogs."/events.txt", $this->getEventsContent());

        file_put_contents($pathToLogs."/bets.txt", $this->getBetsContent());
    }

    private function getEventsContent()
    {
        return file_get_contents($_FILES['events_file']['tmp_name']);
    }

    private function getBetsContent()
    {
        return file_get_contents($_FILES['bets_file']['tmp_name']);
    }
}
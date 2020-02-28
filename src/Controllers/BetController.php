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
use Helpers\TotoHelper;
use Models\BetRequest;
use Models\Event;
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

        $totoJson = TotoHelper::getJsonToto($betRequest->getTotoId());

        $events = EventsHelper::getEventsFromJson($totoJson);




    }

    /**
     * @param Event[] $events
     */
    private function checkEvents(array $events)
    {



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
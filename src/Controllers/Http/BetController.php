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
use Builders\Providers\Factory\NextTotoProviderFactory;
use Helpers\BetRequestHelper;
use Helpers\Http\ClientFactory;
use Helpers\Logger;
use Models\Input\BetRequest;
use Throwable;

class BetController extends Controller
{

    public function makeBet() : void
    {
        $logger = Logger::getInstance();

        $betBuilder = new BetRequestBuilder();

        list($totoId, $book) = explode("_", $_REQUEST['toto_id']);

        $betRequest = $betBuilder->createBetRequest(new BetRequestFromTotoDecision(
            $totoId,
            $book,
            $this->getBetsContent(),
            $this->getEventsContent(),
            (bool)$_REQUEST['is_test']
        ));

        $this->logBet($betRequest);

        if ($book == "fonbet" && !$this->checkPoolForFonBet($this->getEventsContent())) {
            $this->sendResponse(400, 'do not pass conditions');
            return;
        }

        if (!$this->checkEvents($betRequest)) {

            $this->sendResponse(400, 'do not pass conditions');

            return;
        }

        $client = ClientFactory::getClient($book, $totoId, $betRequest->isTest());

        try {
            $client->makeBet($betRequest->getBets());
        }
        catch (Throwable $exception) {
            $logger->log('main', 'Bet was not successful', $exception->getMessage());
            $this->sendResponse(500, $exception->getMessage());
        }

        $betRequestHelper = new BetRequestHelper();

        try {
            $betRequestHelper->saveBetRequest($betRequest, $book);
        }
        catch (Throwable $exception) {
            $logger->log(
                'main',
                'Can\'t save bet request into database',
                $exception->getMessage()
            );
            $this->sendResponse(500, $exception->getMessage());
        }

        $this->sendResponse(200, 'success');
    }


    private function checkEvents(BetRequest $betRequest)
    {
        return true;
    }

    private function checkPoolForFonBet(string $eventsFile)
    {
        $matches = [];

        preg_match_all("/Поставлено пул: (\d+\.\d+)/mu", $eventsFile, $matches);

        $pools = $matches[1];

        $lastPool = (float)array_pop($pools);

        $nextPoolProvider = NextTotoProviderFactory::getNextTotoProvider("fonbet");

        $toto = $nextPoolProvider->getToto();

        if ((($toto->getPot() - $lastPool) / $toto->getPot()) > 0.1) {

            $logger = Logger::getInstance();

            $logger->log("bet", "Refuse to make bet", "Pot is not correct: ".$lastPool." of real pot at the moment: ".$toto->getPot());

            return false;
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
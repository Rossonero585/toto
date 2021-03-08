<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 01/03/20
 * Time: 20:30
 */

namespace Controllers\Http;

use Builders\BetRequestBuilder;
use Builders\Providers\BetRequestFromGenerator;
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

        list($totoId, $book) = explode("_", $_REQUEST['toto_id']);

        $this->logBet($totoId);

        $betBuilder = new BetRequestBuilder();

        $isTest = isset($_REQUEST['is_test']) ? (bool)$_REQUEST['is_test'] : false;

        if ($_REQUEST['is_bet_generator']) {
            $betRequestProvider = new BetRequestFromGenerator(
                $totoId,
                $book,
                $this->getEventsContent(),
                $isTest
            );
        }
        else {
            $betRequestProvider = new BetRequestFromTotoDecision(
                $totoId,
                $book,
                $this->getBetsContent(),
                $this->getEventsContent(),
                $isTest
            );
        }

        $betRequest = $betBuilder->createBetRequest($betRequestProvider);

        if ($book == "fonbet" && !$this->checkPoolForFonBet($this->getEventsContent())) {
            $this->sendResponse(400, 'do not pass conditions');
            return;
        }

        if (!$this->checkEvents($betRequest)) {

            $this->sendResponse(400, 'do not pass conditions');

            return;
        }

        $client = ClientFactory::getClient($book, $totoId, $betRequest->isTest());

        if (count($betRequest->getBets()) > 0) {
            try {
                $client->makeBet($betRequest->getBets());
            }
            catch (Throwable $exception) {
                $logger->log('main', 'Bet was not successful', $exception->getMessage());
                $this->sendResponse(500, $exception->getMessage());
            }
        }
        else {
            $logger->log('main', 'No bets found', 'No bets found');
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
        foreach ($betRequest->getEvents() as $event) {
            if ($event->isCanceled()) return false;
        }

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

        if (!$toto) {

            $logger = Logger::getInstance();

            $logger->log("bet", "Refuse to make bet", "Can't receive next toto");

            return false;
        }

        if ((($toto->getPot() - $lastPool) / $toto->getPot()) > 0.1) {

            $logger = Logger::getInstance();

            $logger->log("bet", "Refuse to make bet", "Pot is not correct: ".$lastPool." of real pot at the moment: ".$toto->getPot());

            return false;
        }

        return true;
    }



    private function logBet(string $totoId)
    {
        $pathToLogs = ROOT_DIR."/log/bet_requests/".$totoId;

        if (!is_dir($pathToLogs)) mkdir($pathToLogs,0777, true);

        file_put_contents($pathToLogs."/events.txt", $this->getEventsContent());

        file_put_contents($pathToLogs."/bets.txt", $this->getBetsContent());
    }

    private function getEventsContent()
    {
        if (isset($_FILES['events_file']['tmp_name'])) {
            return file_get_contents($_FILES['events_file']['tmp_name']);
        }

        return '';
    }

    private function getBetsContent()
    {
        if (isset($_FILES['bets_file']['tmp_name'])) {
            return file_get_contents($_FILES['bets_file']['tmp_name']);
        }

        return '';
    }
}
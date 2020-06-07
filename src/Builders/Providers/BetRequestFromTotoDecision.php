<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 01/03/20
 * Time: 20:35
 */

namespace Builders\Providers;

use Helpers\EventsHelper;
use Helpers\FileParser;
use Helpers\TotoHelper;
use Models\Input\Bet;

class BetRequestFromTotoDecision implements BetRequestInterface
{
    /**
     * @var string
     */
    private $totoId;

    /**
     * @var string
     */
    private $betsFile;

    /**
     * @var string
     */
    private $eventsFile;

    /**
     * @var bool
     */
    private $isTest;

    /**
     * BetRequestFromTotoDecision constructor.
     * @param string $totoId
     * @param string $betsFile
     * @param string $eventsFile
     * @param bool $isTest
     */
    public function __construct(string $totoId, string $betsFile, string $eventsFile, bool $isTest = false)
    {
        $this->totoId     = $totoId;
        $this->betsFile   = $betsFile;
        $this->eventsFile = $eventsFile;
        $this->isTest     = $isTest;
    }

    public function getTotoId() : string
    {
        return $this->totoId;
    }

    public function getBets() : array
    {
        return $this->getBetsArray();
    }

    public function getEvents() : array
    {
        return $this->getEventsArray();
    }

    public function isTest() : bool
    {
        return (bool)$this->isTest;
    }

    private function getBetsArray()
    {
        $betAssoc = FileParser::parseFileWithBets($this->betsFile);

        $betsCount = $_ENV['BETS_COUNT'];

        $betsArray = array_slice($betAssoc, 0, $betsCount);

        return array_map(function (array $arr) {
            return new Bet(
                50,
                str_split($arr['cupon'])
            );
        }, $betsArray);
    }

    private function getEventsArray()
    {
        $eventsAssoc = FileParser::parseFileWithEvents($this->eventsFile);

        $totoJson = TotoHelper::getJsonToto($this->getTotoId());

        return EventsHelper::getEventsFromMixedProvider($totoJson, $eventsAssoc);
    }

}
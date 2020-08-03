<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 01/03/20
 * Time: 20:35
 */

namespace Builders\Providers;

use Builders\EventBuilder;
use Builders\Providers\Factory\DataProviderFactory;
use Builders\Providers\Factory\EventProviderFactory;
use Helpers\FileParser;
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
    private $bookMaker;

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
     * @param string $bookMaker
     * @param string $betsFile
     * @param string $eventsFile
     * @param bool $isTest
     */
    public function __construct(string $totoId, string $bookMaker, string $betsFile, string $eventsFile, bool $isTest = false)
    {
        $this->totoId     = $totoId;
        $this->bookMaker  = $bookMaker;
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
        $betAssoc = FileParser::parseFileWithBets($this->betsFile, $this->bookMaker);

        $betsCount = $_ENV['BETS_COUNT'];

        if ($this->bookMaker == 'fonbet') {
            $betsArray = array_slice($betAssoc, 0, $betsCount);
        }
        else {
            $betsCountForSelecting = $betsCount * 3;

            $selectedBets = array_slice($betAssoc, 0, $betsCountForSelecting);

            usort($selectedBets, function ($arr1, $arr2) {
                $chance9_1 = (float)$arr1['chance_9'];
                $chance9_2 = (float)$arr2['chance_9'];

                if ($chance9_1 === $chance9_2) return 0;

                return $chance9_1 < $chance9_2 ? 1 : -1;
            });

            $betsArray = array_slice($selectedBets, 0, $betsCount);
        }

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

        $dataProvider  = DataProviderFactory::createDataProvider($this->bookMaker, $this->totoId);

        $out = [];

        foreach ($dataProvider->getEvents() as $key => $jsonEvent)
        {
            $number = $key + 1;

            $event = EventBuilder::createEvent(new EventFromMixedSource(
                EventProviderFactory::createEventProvider($this->bookMaker, $jsonEvent, 'ru', $number),
                new EventFromArray($eventsAssoc[$key]),
                $number
            ));

            array_push($out, $event);
        }

        return $out;
    }

}
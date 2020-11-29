<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 01/03/20
 * Time: 20:35
 */

namespace Builders\Providers;

use Helpers\FileParser;
use Models\Input\Bet;

class BetRequestFromTotoDecision extends BetRequest implements BetRequestInterface
{

    /**
     * @var string
     */
    private $betsFile;

    /**
     * @var string
     */
    private $eventsFile;

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
        parent::__construct($totoId, $bookMaker, $isTest);

        $this->betsFile   = $betsFile;
        $this->eventsFile = $eventsFile;
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
        return $this->getEventsArray($this->eventsFile);
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

}
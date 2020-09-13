<?php

namespace Helpers\Http;

use Helpers\Logger;
use Models\Input\Bet;

class TestClient implements ClientInterface
{
    /**
     * @var string
     */
    private $totoId;

    /**
     * @var string
     */
    private $bookMaker;

    public function __construct($totoId, $bookMaker)
    {
        $this->totoId = $totoId;
        $this->bookMaker = $bookMaker;
    }

    /**
     * @param Bet[] $bets
     */
    public function makeBet(array $bets)
    {
        $betContent = "";

        foreach ($bets as $bet) {
            $betContent .= $bet->getMoney().";".implode("", $bet->getResults()).";".PHP_EOL;
        }

        Logger::getInstance()->log('test_bets', "Successfully test bet {$this->totoId} in {$this->bookMaker}", $betContent);
    }

    public function setTokens(): void
    {
        return;
    }


}
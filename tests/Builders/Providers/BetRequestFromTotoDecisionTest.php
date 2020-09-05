<?php


namespace Tests\Builders\Providers;

use Builders\Providers\BetRequestFromTotoDecision;
use Models\Input\Bet;
use PHPUnit\Framework\TestCase;

class BetRequestFromTotoDecisionTest extends TestCase
{
    public function testBetRequestFromTotoDecisionFonbet()
    {
        $provider = new BetRequestFromTotoDecision(
            616,
            'fonbet',
            file_get_contents(__DIR__.'./../../samples/fonbet/matrix.csv'),
            file_get_contents(__DIR__.'./../../samples/fonbet/out.txt'),
            true
        );

        $bets = $provider->getBets();

        /** @var Bet $firstBet */
        $firstBet = array_shift($bets);

        $this->assertEquals(15, count($firstBet->getResults()));
    }

    public function testBetRequestFromTotoDecisionBetcity()
    {
        $provider = new BetRequestFromTotoDecision(
            6459460,
            'betcity',
            file_get_contents(__DIR__.'./../../samples/betcity/matrix.csv'),
            file_get_contents(__DIR__.'./../../samples/betcity/out.txt'),
            true
        );

        $bets = $provider->getBets();

        /** @var Bet $firstBet */
        $firstBet = array_shift($bets);

        $this->assertEquals(14, count($firstBet->getResults()));
    }
}

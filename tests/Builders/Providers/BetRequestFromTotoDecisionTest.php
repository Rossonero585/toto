<?php


namespace Tests\Builders\Providers;

use Builders\Providers\BetRequestFromTotoDecision;
use Models\Input\Bet;
use PHPUnit\Framework\TestCase;

class BetRequestFromTotoDecisionTest extends TestCase
{
    public function testBetRequestFromTotoDecision()
    {
        $provider = new BetRequestFromTotoDecision(
            6459460,
            file_get_contents(__DIR__.'./../../samples/matrix.csv'),
            file_get_contents(__DIR__.'./../../samples/out.txt'),
            true
        );

        $bets = $provider->getBets();

        /** @var Bet $firstBet */
        $firstBet = array_shift($bets);

        $this->assertEquals(14, count($firstBet->getResults()));
    }
}

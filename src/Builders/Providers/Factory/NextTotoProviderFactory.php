<?php


namespace Builders\Providers\Factory;

use Builders\Providers\BetCity\NextTotoProvider as BetCityProvider;
use Builders\Providers\FonBet\NextTotoProvider as FonBetProvider;
use Builders\Providers\NextTotoInterface;
use \Exception;

class NextTotoProviderFactory
{
    /**
     * @param string $bookmaker
     * @return NextTotoInterface
     * @throws Exception
     */
    public static function getNextTotoProvider(string $bookmaker) : NextTotoInterface
    {
        if ('betcity' == $bookmaker) {
            return new BetCityProvider();
        }
        elseif ('fonbet' == $bookmaker) {
            return new FonBetProvider();
        }
        else {
            throw new Exception("Unknown Bookmaker $bookmaker");
        }
    }
}
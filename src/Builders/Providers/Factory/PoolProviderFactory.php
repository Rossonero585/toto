<?php

namespace Builders\Providers\Factory;

use Builders\Providers\BetCity\PoolProvider as BetCityProvider;
use Builders\Providers\FonBet\PoolProvider as FonBetProvider;
use Builders\Providers\PoolProviderInterface;
use \Exception;

class PoolProviderFactory
{
    /**
     * @param string $bookmaker
     * @param int $totoId
     * @return PoolProviderInterface
     * @throws Exception
     */
    public static function createPoolProvider(string $bookmaker, int $totoId) : PoolProviderInterface
    {
        if ('betcity' == $bookmaker) {
            return new BetCityProvider($totoId);
        }
        elseif ('fonbet' == $bookmaker) {
            return new FonBetProvider($totoId);
        }
        else {
            throw new Exception("Unknown Bookmaker $bookmaker");
        }
    }
}
<?php

namespace Builders\Providers\Factory;

use Builders\Providers\DataProviderInterface;
use Builders\Providers\BetCity\DataProvider as BetCityProvider;
use Builders\Providers\FonBet\DataProvider as FonBetProvider;
use \Exception;

class DataProviderFactory
{
    /**
     * @param string $bookmaker
     * @param int $totoId
     * @return DataProviderInterface
     * @throws Exception
     */
    public static function createDataProvider(string $bookmaker, int $totoId) : DataProviderInterface
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
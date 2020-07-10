<?php

namespace Builders\Providers\Factory;

use Builders\Providers\BetCity\TotoFromWeb as BetCityProvider;
use Builders\Providers\FonBet\TotoFromWeb as FonBetProvider;
use Builders\Providers\TotoInterface;
use \stdClass;
use \Exception;

class TotoProviderFactory
{
    /**
     * @param $bookmaker
     * @param stdClass $toto
     * @return TotoInterface
     * @throws Exception
     */
    public static function createTotoProvider($bookmaker, stdClass $toto) : TotoInterface
    {
        if ('betcity' == $bookmaker) {
            return new BetCityProvider($toto);
        }
        elseif ('fonbet' == $bookmaker) {
            return new FonBetProvider($toto);
        }
        else {
            throw new Exception("Unknown Bookmaker $bookmaker");
        }
    }
}
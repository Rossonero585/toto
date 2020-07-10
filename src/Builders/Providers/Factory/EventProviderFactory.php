<?php

namespace Builders\Providers\Factory;

use Builders\Providers\BetCity\EventFromWeb as BetCityProvider;
use Builders\Providers\EventInterface;
use Builders\Providers\FonBet\EventFromWeb as FonBetProvider;
use \stdClass;
use \Exception;

class EventProviderFactory
{
    /**
     * @param string $bookmaker
     * @param stdClass $json
     * @param string $lang
     * @param null $number
     * @return EventInterface
     * @throws Exception
     */
    public static function createEventProvider(string $bookmaker, stdClass $json, $lang = 'en', $number = null) : EventInterface
    {
        if ('betcity' == $bookmaker) {
            return new BetCityProvider($json, $number);
        }
        elseif ('fonbet' == $bookmaker) {
            return new FonBetProvider($json, $lang);
        }
        else {
            throw new Exception("Unknown Bookmaker $bookmaker");
        }
    }
}
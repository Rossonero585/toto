<?php

namespace Helpers\Http;

use \Exception;

class ClientFactory
{
    /**
     * @param $bookMaker
     * @param $totoId
     * @param $isTest
     * @return ClientInterface
     * @throws Exception
     */
    public static function getClient($bookMaker, $totoId, $isTest = false) : ClientInterface
    {
        if ($isTest) {
            return new TestClient($totoId, $bookMaker);
        }

        if ('fonbet' == $bookMaker) {
            return new FonBetClient($totoId);
        }
        elseif ('betcity' == $bookMaker) {
            return new BetCityClient($totoId);
        }

        throw new Exception("Unknown $bookMaker");
    }
}

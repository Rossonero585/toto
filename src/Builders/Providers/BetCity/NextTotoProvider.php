<?php

namespace Builders\Providers\BetCity;

use Builders\Providers\NextTotoInterface;
use Builders\TotoBuilder;
use Models\Toto;

class NextTotoProvider implements NextTotoInterface
{
    public function getToto(): ?Toto
    {
        $betcityUrl = $_ENV['BET_CITY_URL'];

        $content = file_get_contents($betcityUrl."/d/supex/list/cur?page=1&rev=2&ver=54&csn=ooca9s");

        $obj = json_decode($content);

        $toto = null;

        foreach ($obj->reply->totos as $totoObj) {

            $name = $totoObj->name_tt;

            if (mb_strpos($name,"утбол") !== false) {
                return TotoBuilder::createToto(new TotoFromPrevJson($totoObj));
            }
        }

        return null;
    }

}
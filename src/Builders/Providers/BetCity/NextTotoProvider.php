<?php

namespace Builders\Providers\BetCity;

use Builders\Providers\NextTotoInterface;
use Builders\TotoBuilder;
use Models\Toto;
use \DateTime;

class NextTotoProvider implements NextTotoInterface
{
    /** @var Toto */
    private $toto;

    /** @var string */
    private $totoNumber = '';

    public function getToto(): ?Toto
    {
        if (null == $this->toto) {

            $betcityUrl = $_ENV['BET_CITY_URL'];

            $content = file_get_contents($betcityUrl."/d/supers/list/cur?page=1&rev=2&ver=54&csn=ooca9s");

            $obj = json_decode($content);

            foreach ($obj->reply->totos as $totoObj) {

                $name = $totoObj->name_tt;

                if (mb_strpos($name,"утбол") !== false) {

                    $matches = [];

                    preg_match_all("/\((\d)\)$/", $name, $matches);

                    $this->totoNumber = $matches[1][0];

                    $this->toto = TotoBuilder::createToto(new TotoFromPrevJson($totoObj));
                }
            }
        }

        return $this->toto;
    }

    public function getTotoNumber(): string
    {
        $date = new DateTime();

        $dateStr = $date->format('jny');

        return $this->totoNumber ? $dateStr.$this->totoNumber : '';
    }


}
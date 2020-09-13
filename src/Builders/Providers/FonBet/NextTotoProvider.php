<?php

namespace Builders\Providers\FonBet;

use Builders\Providers\NextTotoInterface;
use Builders\TotoBuilder;
use GuzzleHttp\Client;
use Helpers\Http\FonBetClient;
use Models\Toto;

class NextTotoProvider implements NextTotoInterface
{
    public function getToto(): ?Toto
    {

        $client = new Client(["base_uri" => $_ENV['FON_BET_URL'], "headers" => FonBetClient::mainHeaders]);

        $response = $client->post('/superexpress-info-new/DataService.svc/SelectDrawings', [
            'json' => [
                'sp' => [
                    "StartFrom"=> 0,
                    "Count"=> 20,
                    "SortField"=> "Expired",
                    "SortDir"=> "DESC",
                    "Culture"=> "ru-RU",
                    "TimeZoneId"=> "",
                    "TimeZoneOffset"=> -180,
                    "State"=> [2,3],
                ]
            ]
        ]);

        $response = json_decode($response->getBody()->getContents());

        foreach ($response->d->Items as $item) {
            if ($item->State == 2) {
                return TotoBuilder::createToto(new TotoFromWeb($item));
            }
        }

        return null;
    }

}
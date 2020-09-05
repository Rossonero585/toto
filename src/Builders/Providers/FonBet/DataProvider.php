<?php

namespace Builders\Providers\FonBet;

use Builders\Providers\DataProviderInterface;
use Helpers\Http\FonBetClient;
use stdClass;
use GuzzleHttp\Client;
use function \json_decode;

class DataProvider implements DataProviderInterface
{
    /** @var int */
    private $totoId;

    /** @var Client */
    private $client;

    /**
     * @var stdClass
     */
    private $json;

    public function __construct(int $totoId)
    {
        $this->totoId = $totoId;
        $this->client = new Client(["base_uri" => $_ENV['FON_BET_URL'], "headers" => FonBetClient::mainHeaders]);
    }

    public function getEvents(): array
    {
        if (!$this->json) $this->loadJsonData();

        return $this->json->d->Details->Events;
    }

    public function getTotoJson(): stdClass
    {
        if (!$this->json) $this->loadJsonData();

        return $this->json->d;
    }

    private function loadJsonData()
    {
        $content = $this->client->post('/superexpress-info/DataService.svc/GetDrawing', [
                'json' => [
                    'id' => $this->totoId
                ]
            ]
        )->getBody()->getContents();

        $this->json = json_decode($content);
    }

}
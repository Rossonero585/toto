<?php

namespace Builders\Providers\BetCity;

use Builders\Providers\DataProviderInterface;
use GuzzleHttp\Client;
use Helpers\Http\BetCityClient;
use stdClass;
use function json_decode;


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
        $this->client = new Client(["base_uri" => $_ENV['BET_CITY_URL'], "headers" => BetCityClient::mainHeaders]);
    }

    public function getEvents(): array
    {
        if (!$this->json) $this->loadJsonData();

        return $this->json->reply->toto->out;
    }

    public function getTotoJson(): stdClass
    {
        if (!$this->json) $this->loadJsonData();

        return $this->json;
    }

    private function loadJsonData()
    {
        $content = $this->client->get("/d/supers/one?id={$this->totoId}")->getBody()->getContents();

        $this->json = json_decode($content);
    }
}

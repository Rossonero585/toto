<?php

namespace Helpers\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Helpers\Logger;
use Models\Input\Bet;

class BetCityClient
{
    const bet_city_cache_file = 'token.txt';

    const dev = "b63d27af6554ac2a54869acd63300289";
    const ver = "179";
    const csn = "ooca9s";

    const authUrl = "/d/user/auth";
    const check = "/d/supex/check";
    const bet = "/d/supex/check_cmx";
    const getToto = "/d/supex/one";

    const mainHeaders = [
        "Accept" => "application/json, text/plain, */*",
        "Content-Type" => "application/x-www-form-urlencoded",
        "Origin" => "https://betcity.ru",
        "Referer" => "https://betcity.ru/",
        "User-Agent" => "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.131 Safari/537.36"
    ];

    /**
     * Guzzle clinet object
     * @var Client
     */
    private $client;

    /**
     * Toto id
     * @var integer
     */
    private $totoId;

    /**
     * Auth token
     * @var string
     */
    private $token;

    /**
     * Internal toto hash
     * @var string
     */
    private $totoHash;

    /**
     * Money on the account
     * @var float
     */
    private $money;

    public function __construct($totoId)
    {
        $this->totoId = $totoId;
        $this->client = new Client(["base_uri" => $_ENV['BET_CITY_URL'], "headers" => self::mainHeaders]);
    }

    /**
     * @throws \Throwable
     */
    public function setTokens() : void
    {
        if ($this->token && $this->totoHash) return;

        $tokensFromFile = $this->readTokensFromFile();

        if ($tokensFromFile) {
            $this->token = $tokensFromFile[0];
            $this->totoHash = $tokensFromFile[1];
            return;
        };

        $authRequest = $this->client->postAsync(self::authUrl, [
            "query" => [
                "info" => 2,
                "settings" => 1,
                "dev" => self::dev,
                "ver" => self::ver,
                "csn" => self::csn
            ],
            "form_params" => [
                "user" => $_ENV['BET_CITY_LOGIN'],
                "pass" => $_ENV['BET_CITY_PASSWORD']
            ]
        ]);

        $totoRequest = $this->client->getAsync(self::getToto, [
            "query" => [
                "id" => $this->totoId
            ]
        ]);

        $promises = [
            "auth" => $authRequest,
            "toto" => $totoRequest
        ];

        try {
            $results = Promise\unwrap($promises);

            /** @var \Psr\Http\Message\ResponseInterface $authResponse */
            $authResponse = $results["auth"];
            /** @var \Psr\Http\Message\ResponseInterface $totoResponse */
            $totoResponse = $results["toto"];

            $this->totoHash = \GuzzleHttp\json_decode($totoResponse->getBody()->getContents())->reply->hash;

            $userInfo = \GuzzleHttp\json_decode($authResponse->getBody()->getContents())->reply;

            $this->token = $userInfo->token;

            $this->money = $userInfo->info->money->avail;

            $this->writeTokensToFile($this->token, $this->totoHash);

        }
        catch (\Throwable $exception) {
            Logger::getInstance()->log('http', "Auth request to betcity failed {$this->totoId}", $exception->getMessage());
            throw $exception;
        }

    }

    /**
     * @param array $bets
     * @param bool $isTest
     * @throws \Throwable
     */
    public function makeBet(array $bets, $isTest = false)
    {
        $this->setTokens();

        $betContent = "";

        /** @var Bet $bet */
        foreach ($bets as $bet) {

            $betContent .= $bet->getMoney().";";

            foreach ($bet->getResults() as $i => $result) {
                $betContent .= ($i + 1)."=".$result.";";
            }

            $betContent .= PHP_EOL;
        }

        if (!$isTest) {
            $result = $this->client->post(self::bet, [
                "query" => [
                    "ver" => self::ver,
                    "csn" => self::csn,
                    "token" => $this->token,
                    "id" => $this->totoId
                ],
                "form_params" => [
                    "hash" => $this->totoHash,
                    "content" => $betContent
                ]
            ]);

            $content = $result->getBody()->getContents();

            /** @var \stdClass $reply */
            $reply = \GuzzleHttp\json_decode($content)->reply;

            if ($reply->errors_count > 0) {
                Logger::getInstance()->log('http', "Fail to bet {$this->totoId}", $content);
            }
            else {
                Logger::getInstance()->log('http', "Successfully bet {$this->totoId}", $content);
            }
        }
        else {
            Logger::getInstance()->log('test_bets', "Successfully test bet {$this->totoId}", $betContent);
        }

    }

    public function writeTokensToFile($token, $totoHash) : void
    {
        file_put_contents(self::bet_city_cache_file, $token.PHP_EOL);
        file_put_contents(self::bet_city_cache_file, $totoHash, FILE_APPEND);
    }

    public function readTokensFromFile() : ?array
    {
        if (file_exists(self::bet_city_cache_file)) {

            $content = file_get_contents(self::bet_city_cache_file);

            if ($content) {

                $updateTime = filemtime(self::bet_city_cache_file);

                if ((time() - $updateTime) < 3600) {
                    return explode(PHP_EOL, $content);
                }
            }
        }

        return null;
    }
}
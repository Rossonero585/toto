<?php

namespace Helpers\Http;

use GuzzleHttp\Client;
use Helpers\ArrayHelper;
use Helpers\Logger;
use Models\Input\Bet;
use \stdClass;


class FonBetClient implements ClientInterface
{
    const fon_bet_token_file = 'fonbet_auth_response.json';

    const mainHeaders = [
        "Accept" => "application/json, text/plain, */*",
        "Content-Type" => "application/json",
        "Origin" => "https://www.fonbet.ru",
        "Referer" => "https://www.fonbet.ru/products/superexpress-info/?locale=ru&pageDomain=https://www.fonbet.ru",
        "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36"
    ];


    /**
     * Toto id
     * @var integer
     */
    private $totoId;

    /**
     * Guzzle client object
     * @var Client
     */
    private $client;

    /**
     * @var \stdClass
     */
    private $authResponse;


    public function __construct($totoId)
    {
        $this->totoId = $totoId;
        $this->client = new Client(["base_uri" => $_ENV['FON_BET_URL'], "headers" => self::mainHeaders]);
    }

    public function setTokens() : void
    {
        $this->getAuthResponse();
    }

    public function makeBet(array $bets)
    {
        $this->getAuthResponse();

        $clientInfo = [
            "client" => [
                "id" => $this->authResponse->clientId,
            ],
            "clientId" => $this->authResponse->clientId,
            "fsid" => $this->authResponse->fsid,
            "sysId" => 1,
            "lang" => "ru"
        ];

        /** @var Bet $bet */
        foreach ($bets as $bet) {

            $register = $this->client->post("/session/toto/requestId", [
                "json" => $clientInfo
            ]);

            $response = $register->getBody()->getContents();

            $jsonRegisterResponse = json_decode($response);

            $registerId = $jsonRegisterResponse->requestId;

            $betResponse = $this->client->post("/session/toto/register", [
                "json" => $clientInfo + [
                    "coupon" => [
                        "amount" => $bet->getMoney(),
                        "totoId" => $this->totoId,
                        "win1Mask" => ArrayHelper::convertToFonBetFormat($bet->getResults(), "1"),
                        "win2Mask" => ArrayHelper::convertToFonBetFormat($bet->getResults(), "2"),
                        "drawMask" => ArrayHelper::convertToFonBetFormat($bet->getResults(), "X")
                    ],
                    "requestId" => $registerId
                ]
            ]);

            $betResponseContent = $betResponse->getBody()->getContents();

            /** @var \stdClass $reply */
            $reply = \GuzzleHttp\json_decode($betResponseContent);

            if ($reply->resultCode != 0) {
                Logger::getInstance()->log('http',
                    "Fail to bet {$this->totoId}",
                    implode("", $bet->getResults()).PHP_EOL.$betResponseContent
                );
            }
            else {
                Logger::getInstance()->log('http',
                    "Successfully bet {$this->totoId}",
                    implode("", $bet->getResults())
                );
            }
        }
    }

    private function getAuthResponse()
    {
        $content = $this->readTokensFromFile();

        if (null === $content) $this->setAuthResponse();

        $this->authResponse = json_decode($content);
    }

    private function setAuthResponse()
    {
        function randomFloat($min = 0, $max = 1) {
            return $min + mt_rand() / mt_getrandmax() * ($max - $min);
        }

        $defaultJson = new stdClass();

        $defaultJson->sysId = 1;
        $defaultJson->random = randomFloat()." :)";
        $defaultJson->sign = "secret password";
        $defaultJson->deviceId = $_ENV["FON_BET_DEVICE_ID"];
        $defaultJson->fingerprintHash = $_ENV["FON_BET_FINGER_PRINT"];
        $defaultJson->lang = "ru";
        $defaultJson->mail = $_ENV["FON_BET_LOGIN"];

        $hashPassword = hash("sha512", $_ENV["FON_BET_PASSWORD"]);

        $s = json_encode($defaultJson);

        $sign = hash_hmac("sha512", $s, $hashPassword);

        $defaultJson->sign = $sign;

        $response = $this->client->post('/session/loginByMail', [
            'json' => $defaultJson
        ]);

        $content = $response->getBody()->getContents();

        if ($response->getStatusCode() === 200) {
            $this->writeResponseToFile($content);
        }
    }

    private function writeResponseToFile(string $response)
    {
        file_put_contents(self::fon_bet_token_file, $response);
    }

    private function readTokensFromFile()
    {
        if (file_exists(self::fon_bet_token_file)) {

            $content = file_get_contents(self::fon_bet_token_file);

            if ($content) {

                $updateTime = filemtime(self::fon_bet_token_file);

                if ((time() - $updateTime) < 3600) {
                    return $content;
                }
            }
        }

        return null;
    }

}

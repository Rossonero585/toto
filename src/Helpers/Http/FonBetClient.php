<?php

namespace Helpers\Http;

class FonBetClient implements ClientInterface
{
    const mainHeaders = [
        "Accept" => "application/json, text/plain, */*",
        "Content-Type" => "application/json",
        "Origin" => "https://www.fonbet.ru",
        "Referer" => "https://www.fonbet.ru/products/superexpress-info/?locale=ru&pageDomain=https://www.fonbet.ru",
        "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36"
    ];

    public function makeBet(array $bets)
    {
        // TODO: Implement makeBet() method.
    }


}

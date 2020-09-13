<?php

namespace Helpers\Http;


interface ClientInterface
{
    public function makeBet(array $bets);

    public function setTokens(): void;
}
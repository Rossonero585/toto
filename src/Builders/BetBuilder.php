<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 01/03/20
 * Time: 20:42
 */

namespace Builders;

use Builders\Providers\BetRequestInterface;
use Models\BetRequest;

class BetBuilder
{
    public function createBetRequest(BetRequestInterface $betRequestProvider)
    {
        return new BetRequest(
            $betRequestProvider->getTotoId(),
            $betRequestProvider->getBetsFile(),
            $betRequestProvider->getEventsFile(),
            $betRequestProvider->isTest()
        );
    }
}
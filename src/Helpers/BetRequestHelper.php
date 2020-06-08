<?php

namespace Helpers;

use Builders\Providers\TotoFromWeb;
use Builders\TotoBuilder;
use Models\BetPackage;
use Models\Input\Bet;
use Models\Input\BetRequest;
use Repositories\BetItemRepository;
use Repositories\BetPackageRepository;
use Repositories\EventRepository;
use Repositories\Repository;
use Repositories\TotoRepository;

class BetRequestHelper
{
    public function saveBetRequest(BetRequest $betRequest)
    {
        $totoFromWeb = new TotoFromWeb(TotoHelper::getJsonToto($betRequest->getTotoId()));

        $toto = TotoBuilder::createToto($totoFromWeb);

        /** @var TotoRepository $totoRepository */
        $totoRepository = Repository::getRepository(TotoRepository::class);

        $totoRepository->addToto($toto);

        /** @var EventRepository $eventsRepository */
        $eventsRepository = Repository::getRepository(EventRepository::class);

        foreach ($betRequest->getEvents() as $event) $eventsRepository->addEvent($event);

        /** @var BetItemRepository $betItemRepository */
        $betItemRepository = Repository::getRepository(BetItemRepository::class);

        $package = $this->createBetPackage($betRequest->getBets(), $betRequest->isTest());

        /** @var Bet $bet */
        foreach ($betRequest->getBets() as $bet) {
            $betItemRepository->addBetItem($bet, $package->getId());
        }

    }

    /**
     * @param array $bets
     * @param bool $isTest
     * @return BetPackage
     * @throws \Exceptions\UnknownRepository
     */
    private function createBetPackage(array $bets, $isTest = false)
    {
        $money = array_reduce($bets, function ($carry, Bet $bet) {
            return $carry + $bet->getMoney();
        }, 0);

        /** @var BetPackageRepository $betPackageRepository */
        $betPackageRepository = Repository::getRepository(BetPackageRepository::class);

        return $betPackageRepository->addNewPackage($money, new \DateTime(), $isTest);

    }
}
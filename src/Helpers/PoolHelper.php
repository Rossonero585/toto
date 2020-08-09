<?php

namespace Helpers;

use Builders\BreakDownBuilder;
use Models\BetPackage;
use Repositories\BetItemRepository;
use Repositories\BetPackageRepository;
use Repositories\PoolRepository;
use Repositories\Repository;

class PoolHelper
{
    /** @var PoolRepository */
    private $poolRepository;

    /** @var BetItemRepository */
    private $betItemRepository;

    /** @var BetPackageRepository */
    private $betPackageRepository;

    /** @var array */
    private $testBets;

    public function __construct()
    {
        $this->poolRepository = Repository::getRepository(PoolRepository::class);
        $this->betItemRepository = Repository::getRepository(BetItemRepository::class);
        $this->betPackageRepository = Repository::getRepository(BetPackageRepository::class);
    }

    public function getWinnersBreakDown(array $results, bool $includeTest = false, $fastMatch = true)
    {
        $pool = $this->poolRepository->getAllPool();

        $pool = array_merge($pool, $includeTest ? $this->getTestBets() : []);

        $outArray = [];

        if ($fastMatch) {
            $compareFunction = function (array $a, array $b) {
                return ArrayHelper::countMatchValues($a, $b);
            };
        }
        else {
            $compareFunction = function (array $a, array $b) {
                return ArrayHelper::countMatchResult($a, $b);
            };
        }

        foreach ($pool as $poolItem) {

            $money = (float)$poolItem['money'];

            $matched = $compareFunction($results, str_split($poolItem['result']));

            if (!isset($outArray[$matched])) {
                $outArray[$matched] = [
                    'amount' => $matched,
                    'pot' => 0
                ];
            }

            $outArray[$matched]['pot'] += $money;
        }

        $breakDown = BreakDownBuilder::createBreakDownFromArray($outArray);

        return $breakDown;
    }

    private function getTestBets()
    {
        if (!$this->testBets) {

            $betPackages = $this->betPackageRepository->getAllPackages();

            $testBets = [];

            /** @var BetPackage $package */
            foreach ($betPackages as $package) {

                if ($package->isTest()) {

                    $betItems = $this->betItemRepository->geBetsOfPackage($package->getId());

                    foreach ($betItems as $item) {
                        array_push($testBets, [
                            'money' => $item->getMoney(),
                            'result' => implode("", $item->getResults())
                        ]);
                    }
                }
            }

            $this->testBets = $testBets;
        }

        return $this->testBets;
    }
}
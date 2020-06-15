<?php

namespace Helpers;

use Builders\BreakDownBuilder;
use Models\BetPackage;
use Models\BreakDown;
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

    /** @var  BreakDown[] */
    private $breakDownsWithTest = [];

    /** @var  BreakDown[] */
    private $breakDowns = [];

    /** @var array */
    private $testBets;

    public function __construct()
    {
        $this->poolRepository = Repository::getRepository(PoolRepository::class);
        $this->betItemRepository = Repository::getRepository(BetItemRepository::class);
        $this->betPackageRepository = Repository::getRepository(BetPackageRepository::class);
    }

    public function getWinnersBreakDown(array $results, bool $includeTest = false)
    {
        $cachedBreakDown = $this->getCachedBreakDown($results, $includeTest);

        if ($cachedBreakDown) return $cachedBreakDown;

        $pool = $this->poolRepository->getAllPool();

        $pool = array_merge($pool, $includeTest ? $this->getTestBets() : []);

        $outArray = [];

        foreach ($pool as $poolItem) {

            $money = (float)$poolItem['money'];

            $matched = ArrayHelper::countMatchResult($results, str_split($poolItem['result']));

            if (!isset($outArray[$matched])) {
                $outArray[$matched] = [
                    'amount' => $matched,
                    'pot' => 0
                ];
            }

            $outArray[$matched]['pot'] += $money;
        }

        $breakDown = BreakDownBuilder::createBreakDownFromArray($outArray);

        $this->addCachedBreakDown($results, $breakDown);

        return $breakDown;
    }

    /**
     * @param array $results
     * @param bool $isTest
     * @return BreakDown|null
     */
    private function getCachedBreakDown(array $results, bool $isTest)
    {
        if ($isTest) {
            $cachedArray = &$this->breakDownsWithTest;
        }
        else {
            $cachedArray = &$this->breakDowns;
        }

        $key = md5(json_encode($results));

        if (isset($cachedArray[$key])) {
            return $cachedArray[$key];
        }

        return null;
    }

    private function addCachedBreakDown(array $results, BreakDown $breakDown)
    {
        $key = md5(json_encode($results));

        $this->breakDowns[$key] = $breakDown;

        return $this;
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
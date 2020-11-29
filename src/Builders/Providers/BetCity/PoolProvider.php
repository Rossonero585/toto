<?php

namespace Builders\Providers\BetCity;

use Builders\Providers\PoolProviderInterface;
use \Generator;

class PoolProvider implements PoolProviderInterface
{
    /**
     * @var string
     */
    private $dump;

    public function __construct(int $totoId)
    {
        $this->dump = file_get_contents($_ENV['BET_CITY_URL']."/supers/dump/$totoId.txt");
    }

    public function getPoolItem() : Generator
    {
        $lines = explode("\n", $this->dump);

        foreach ($lines as $line) {

            $poolItemsProvider = new PoolItemsFromCoupon($line);

            foreach ($poolItemsProvider->getPoolItems() as $poolItem) {
                yield $poolItem;
            }
        }
    }

}
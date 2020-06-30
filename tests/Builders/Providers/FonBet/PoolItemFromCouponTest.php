<?php

namespace Tests\Builders\Providers\FonBet;

use Builders\Providers\FonBet\PoolItemsFromCoupon;
use Models\PoolItem;
use PHPUnit\Framework\TestCase;

class PoolItemFromCouponTest extends TestCase
{
    private function createPoolItemProvider()
    {
        return new PoolItemsFromCoupon(
            json_decode(file_get_contents(__DIR__ . "./../../../samples/fonbet/poolItem.json"))
        );
    }

    public function testPoolItemProvider()
    {
        $poolItemProvider = $this->createPoolItemProvider();

        $poolItems = $poolItemProvider->getPoolItems();

        $this->assertEquals(32, count($poolItems));

        /** @var PoolItem $poolItem */
        $poolItem = array_pop($poolItems);

        $this->assertEquals($poolItem->getMoney(), 1605 / 32);

        $this->assertEquals(strlen($poolItem->getResult()), 15);
    }
}
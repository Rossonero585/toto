<?php

namespace Tests\Builders\Providers\BetCity;

use Builders\Providers\BetCity\PoolItemsFromCoupon;
use Models\PoolItem;
use PHPUnit\Framework\TestCase;

class PoolItemFromCouponTest extends TestCase
{
    public function createPoolItemFromCoupon()
    {
        return new PoolItemsFromCoupon("LOLIZJOMZE|30.06.2020 12:34:20|400 руб|X,2;1;X;1;1;X;1;X;1;X;1,X;X,2;X;X;");
    }

    public function testPoolItemFromCoupon()
    {
        $poolItemFromCoupon = $this->createPoolItemFromCoupon();

        $poolItems = $poolItemFromCoupon->getPoolItems();

        $this->assertEquals(8, count($poolItems));

        /** @var PoolItem $poolItem */
        $poolItem = array_pop($poolItems);

        $this->assertEquals(50, $poolItem->getMoney());

        $this->assertEquals(14, strlen($poolItem->getResult()));

        $this->assertEquals("30.06.2020 12:34:20", $poolItem->getBetDate()->format('d.m.Y H:i:s'));

    }
}
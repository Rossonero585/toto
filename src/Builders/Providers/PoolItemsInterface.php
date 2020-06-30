<?php

namespace Builders\Providers;

use Models\PoolItem;

interface PoolItemsInterface
{
    /**
     * @return PoolItem[]
     */
    public function getPoolItems() : array;
}

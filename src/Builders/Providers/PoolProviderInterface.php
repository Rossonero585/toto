<?php

namespace Builders\Providers;

use \Generator;

interface PoolProviderInterface
{
    public function getPoolItem() : Generator;
}
<?php

namespace Builders\Providers;

use \stdClass;

interface DataProviderInterface
{
    public function getEvents() : array;

    public function getTotoJson() : stdClass;
}
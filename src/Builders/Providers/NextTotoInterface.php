<?php

namespace Builders\Providers;

use Models\Toto;

interface NextTotoInterface
{
    public function getToto() : ?Toto;

    public function getTotoNumber() : string;
}
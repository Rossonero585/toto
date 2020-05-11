<?php

namespace Exceptions;

use Exception;

class UndefinedTotoId extends Exception
{
    public function __construct()
    {
        parent::__construct('Toto is is not recognized');
    }
}

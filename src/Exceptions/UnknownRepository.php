<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 15/12/19
 * Time: 21:48
 */

namespace Exceptions;

class UnknownRepository extends \Exception
{
    public function __construct(string $name)
    {
        parent::__construct("Unknown repository: $name");
    }
}
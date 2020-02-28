<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 01/03/20
 * Time: 20:31
 */

namespace Builders\Providers;

interface BetRequestInterface
{
    public function getTotoId() : int ;

    public function getBetsFile() : string;

    public function getEventsFile() : string;

    public function isTest() : bool;
}
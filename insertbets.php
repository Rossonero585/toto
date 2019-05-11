<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 11/05/19
 * Time: 12:38
 */

include "vendor/autoload.php";

$cu = new \Controllers\UpdateController();

$start = mktime();

$cu->insertMyBetsAction("bet.txt");

$end = mktime();

echo PHP_EOL;
echo ($end - $start);
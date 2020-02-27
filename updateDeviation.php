<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 27/02/20
 * Time: 22:40
 */

include "autoload.php";

$option = getopt("t:");

$totoId = $option['t'];

$cu = new \Controllers\UpdateController();

$cu->updateDeviation();

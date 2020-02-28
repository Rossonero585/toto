<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 28/02/20
 * Time: 22:52
 */

include "autoload.php";

$option = getopt("t:");

$totoId = $option['t'];

$cu = new \Controllers\UpdateController();

$cu->updateRandomBetsUsingSeveralProcesses($totoId);

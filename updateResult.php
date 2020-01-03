<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 12/05/19
 * Time: 15:54
 */

include "autoload.php";

$option = getopt("t:");

$totoId = $option['t'];

$cu = new \Controllers\UpdateController();

$cu->updateTotoResult($totoId);

<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 30/09/17
 * Time: 13:18
 */

include "autoload.php";

$option = getopt("t:");

$totoId = $option['t'];

$cu = new \Controllers\UpdateController();

$cu->initAction($totoId);

$cu->insertEventAction($totoId);

$cu->insertBetsAction($totoId);

//$cu->updateBreakDownsAction();
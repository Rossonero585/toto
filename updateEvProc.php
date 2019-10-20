<?php

include "autoload.php";

$options = getopt("t:");

$totoId = $options['t'];

$cu = new \Controllers\UpdateController();

$cu->updateBetsEvUsingSeparateProcess($totoId);
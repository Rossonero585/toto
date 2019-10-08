<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 12/05/19
 * Time: 15:54
 */

include "autoload.php";

$options = getopt(null, ['id:']);

$cu = new \Controllers\UpdateController();

$cu->updateBetsEv();

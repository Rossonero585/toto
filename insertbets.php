<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 11/05/19
 * Time: 12:38
 */

include "autoload.php";

$options = getopt(null, ['file:']);

$cu = new \Controllers\UpdateController();

$cu->insertMyBetsAction($options['file']);

<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 07/01/20
 * Time: 10:20
 */


include "autoload.php";

$option = getopt("s:");

$sql = $option['s'];

$mc = new \Controllers\ModifyController();

$mc->modifySchema($sql);

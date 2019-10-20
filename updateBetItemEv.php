<?php


include "autoload.php";

$options = getopt("t:", ["id:", "type:"]);

$totoId = $options['t'];

$betItemId = $options['id'];

$calculationType = $options['type'];

$cu = new \Controllers\UpdateController();

test_log("Start calculate bet item with id $betItemId for $totoId using $calculationType. Pid".getmypid());

$cu->updateBetItemById($betItemId, $calculationType);

test_log("End calculate bet item with id $betItemId for $totoId using $calculationType. Pid".getmypid());

function test_log($str) {

    $str  = (new DateTime())->format(DATE_ISO8601)." - ".$str." - ".PHP_EOL;

    file_put_contents("test.log", $str, FILE_APPEND);
}

<?php


include "autoload.php";

$options = getopt("t:", ["id:"]);

$totoId = $options['t'];

$betItemId = $options['id'];

$cu = new \Controllers\UpdateController();

test_log("Start calculate bet item with id $betItemId for $totoId. Pid".getmypid());

$cu->updateBetItemById($betItemId);

test_log("End calculate bet item with id $betItemId for $totoId. Pid".getmypid());

function test_log($str) {

    $str  = (new DateTime())->format(DATE_ISO8601)." - ".$str." - ".PHP_EOL;

    file_put_contents("test.log", $str, FILE_APPEND);
}

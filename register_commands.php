<?php

include "autoload.php";

use Controllers\CalculationController;
use Controllers\Http\BetController;
use Controllers\UpdateController;
use Controllers\CheckController;
use Helpers\Arguments;
use Helpers\CommandManager;

$commandManager = new CommandManager();

$arguments = Arguments::getArguments($argv);


$commandManager->registerCommand('insertion', function () use ($arguments) {

    $totoId = $arguments->get('t');

    list($totoId, $bookmaker) = explode("_", $totoId);

    $cu = new UpdateController();

    $cu->insertTotoAction($bookmaker, $totoId);

    $cu->insertEventsAction($bookmaker, $totoId);

    $cu->insertBetsAction($bookmaker, $totoId);

});

$commandManager->registerCommand('schedule', function () {

    $cc = new CheckController();

    $cc->scheduleToto();

});

$commandManager->registerCommand('update_bet_item_ev', function () use ($arguments) {

    $totoId = $arguments->get('t');

    $betItemId = $arguments->get('id');

    $cu = new UpdateController();

    test_log("Start calculate bet item with id $betItemId for $totoId. Pid".getmypid());

    $cu->updateBetItemById($betItemId);

    test_log("End calculate bet item with id $betItemId for $totoId. Pid".getmypid());

});

$commandManager->registerCommand('update_deviation', function () {

    $cu = new UpdateController();

    $cu->updateDeviation();

});

$commandManager->registerCommand('update_ev', function () {

    $cu = new UpdateController();

    $cu->updateBetsEv();

});

$commandManager->registerCommand('update_ev_proc', function () use ($arguments) {

    $totoId = $arguments->get('t');

    $cu = new UpdateController();

    $cu->updateBetsEvUsingSeparateProcess($totoId);

});

$commandManager->registerCommand('update_random_bet_ev', function () use ($arguments) {

    $totoId = $arguments->get('t');

    $cu = new UpdateController();

    $cu->updateRandomBetsUsingSeveralProcesses($totoId);

});


$commandManager->registerCommand('update_temp', function () use ($arguments) {

    $totoId = $arguments->get('t');

    $cu = new UpdateController();

    $cu->updateAvgPAndDeviation($totoId);

});


$commandManager->registerCommand('update_result', function () use ($arguments) {

    $totoId = $arguments->get('t');

    list($totoId, $bookmaker) = explode("_", $totoId);

    $cu = new UpdateController();

    $cu->updateTotoResult($totoId, $bookmaker);

});


$commandManager->registerCommand('make_bet', function () {

    $cu = new BetController();

    $cu->makeBet();

});

$commandManager->registerCommand('save_tokens', function () use($arguments) {

    $totoId = $arguments->get('t');

    $cu = new UpdateController();

    $cu->saveTokens($totoId);

});


$commandManager->registerCommand('calculate_ratio', function () use($arguments) {

    $result = $arguments->get('r');

    $cat = $arguments->get('c');

    $cu = new CalculationController();

    $start = microtime(true);

    echo $cu->calculateRatioAction(str_split($result), (int)$cat);

    echo PHP_EOL;

    $end = microtime(true);

    echo ($end - $start);
});


$commandManager->registerCommand('calculate_package_by_categories', function () use($arguments) {

    $cu = new CalculationController();

    $p = $cu->calculateProbabilityOfPackageByCategories($arguments->get('id'));

    var_dump($p);
});



$commandManager->registerCommand('test_bet_generator', function () use ($arguments) {

    $cu = new CalculationController();

    $cu->testBetGenerator();

});

function test_log($str) {

    $str  = (new DateTime())->format(DATE_ISO8601)." - ".$str." - ".PHP_EOL;

    file_put_contents("test.log", $str, FILE_APPEND);
}

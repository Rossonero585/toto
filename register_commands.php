<?php

include "autoload.php";

use Controllers\Http\BetController;
use Controllers\UpdateController;
use Controllers\CheckController;
use Controllers\ViewController;
use Helpers\Arguments;
use Helpers\CommandManager;

$commandManager = new CommandManager();

$arguments = Arguments::getArguments($argv);


$commandManager->registerCommand('insertion', function () use ($arguments) {

    $totoId = $arguments->get('t');

    list($totoId, $bookmaker) = explode("_", $totoId);

    $cu = new UpdateController();

    $cu->insertEventAction($totoId);

    $cu->insertBetsAction($totoId);

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

    function test_log($str) {

        $str  = (new DateTime())->format(DATE_ISO8601)." - ".$str." - ".PHP_EOL;

        file_put_contents("test.log", $str, FILE_APPEND);
    }

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


$commandManager->registerCommand('update_result', function () use ($arguments) {

    $totoId = $arguments->get('t');

    list($totoId, $bookmaker) = explode("_", $totoId);

    $cu = new UpdateController();

    $cu->updateTotoResult($totoId);

});


$commandManager->registerCommand('make_bet', function () {

    $cu = new BetController();

    $cu->makeBet();

});

$commandManager->registerCommand('betcity_tokens', function () use($arguments) {

    $totoId = $arguments->get('t');

    list($totoId, $bookmaker) = explode("_", $totoId);

    $cu = new UpdateController();

    $cu->setBetCityTokens($totoId);

});


<?php

include "autoload.php";

use Controllers\Http\BetController;
use Controllers\ViewController;
use Helpers\CommandManager;

$commandManager = new CommandManager();

$commandManager->registerCommand('dump', function () {

    $cv = new ViewController();

    $cv->getAll();

});

$commandManager->registerCommand('make_bet', function () {

    $cu = new BetController();

    $cu->makeBet();

});


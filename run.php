<?php

use Helpers\Arguments;

include "register.php";

$arguments = Arguments::getArguments($argv);

$commandManager->runCommand($arguments->getCommand());

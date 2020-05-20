<?php

use Helpers\Arguments;

include "register_commands.php";

$arguments = Arguments::getArguments($argv);

$commandManager->runCommand($arguments->getCommand());

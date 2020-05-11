<?php

namespace Helpers;

use Exceptions\UndefinedTotoId;

/**
 * @return int
 * @throws UndefinedTotoId
 */
function getTotoId() : int {

    $opt = getopt('t:');

    if (isset($_SESSION['toto_id'])) {
        return $_SESSION['toto_id'];
    }
    else if (isset($_REQUEST['toto_id'])) {
        return $_REQUEST['toto_id'];
    }
    else if (isset($opt['t'])) {
        return $opt['t'];
    }

    throw new UndefinedTotoId();

}
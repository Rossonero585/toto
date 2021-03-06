<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 12/05/19
 * Time: 14:38
 */

include "vendor/autoload.php";

const ROOT_DIR = __DIR__;


$content = file_get_contents(__DIR__."/.env");

$lines = explode(PHP_EOL, $content);

foreach ($lines as $line) {

    if (!$line) continue;

    $params = explode("=", trim($line));
    $_ENV[$params[0]] = $params[1];
}

ini_set('error_log', 'php_error.log');
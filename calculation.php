<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 21/10/17
 * Time: 16:25
 */

include "vendor/autoload.php";

$cc = new \Controllers\CalculationController();
////
////$bet = ['2', '2', '1', '1', '2', '2', 'x', '1', '1', 'x', 'x', '1', '2', 'x'];
////
////$cc->calculateEV($bet, 50);
////
//
////$bet = "X22X211111221X";
//
//$bet = ['x','x',2,'x',1,2,2,2,1,1,2,2,1,2];
//
//list($ev, $p) = $cc->calculateEV($bet, 50);
//
//file_put_contents(__DIR__."/stats/ev_5501373.log", $ev.": ".$p.PHP_EOL, FILE_APPEND);
//
//
////X22X222111221X
//
//$bet = ['x',2,2,'x',2,2,2,1,1,1,2,2,1,'x'];
//
//list($ev, $p) = $cc->calculateEV($bet, 50);
//
//file_put_contents(__DIR__."/stats/ev_5501373.log", $ev.": ".$p.PHP_EOL, FILE_APPEND);
//

echo $cc->calculateProbabilityOfPackage("bet.txt");
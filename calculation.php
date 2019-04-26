<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 21/10/17
 * Time: 16:25
 */

include "vendor/autoload.php";

$cc = new \Controllers\CalculationController();

$bet = ['2', '2', '1', '1', '2', '2', 'x', '1', '1', 'x', 'x', '1', '2', 'x'];

$cc->calculateEV($bet, 50);


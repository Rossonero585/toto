<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 04/02/18
 * Time: 20:19
 */

CONST url = "https://hdr.betcity.ru/d/se/one?rev=2&id=4692879&type=11&ver=449";

$r = file_get_contents(url);

$toto = json_decode($r);

$toto = $toto->reply->toto;

$vcount = $toto->vcount;

$pot = $toto->pool;

$time = date("Y-m-d H:i:s");

file_put_contents("pot2.log", $time.";".$vcount.";".$pot.PHP_EOL, FILE_APPEND);
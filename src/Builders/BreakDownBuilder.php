<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 21/10/17
 * Time: 17:42
 */

namespace Builders;

use Models\BreakDown;
use Models\BreakDownItem;

class BreakDownBuilder
{
    public static function createBreakDownFromArray(array $a)
    {
        $breakDownItems = [];

        $pot = 0;

        foreach ($a as $item) {
            $pot = $pot + $item['pot'];
            $count = $item['amount'];
            $breakDownItems[] = new BreakDownItem($count, $pot);
        }

        $breakDown = new BreakDown($breakDownItems);

        return $breakDown;
    }

}
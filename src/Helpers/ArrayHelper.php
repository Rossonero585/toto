<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 14/10/17
 * Time: 23:34
 */

namespace Helpers;

class ArrayHelper
{
    public static function array_combination(array $arr) {
        if (count($arr) < 2) return $arr;

        $out = array_map(function($a) {return [$a];} , $arr[0]);

        for ($i = 1; $i < count($arr); $i++) {
            $tempArr = [];
            foreach ($out as $outItem) {
                foreach (self::arr_comb($outItem, $arr[$i]) as $tempItem) {
                    $tempArr[] = $tempItem;
                }
            }
            $out = $tempArr;
        }

        return $out;
    }

    private static function arr_comb(array $arr1, array $arr2) {
        $out = array();

        foreach ($arr2 as $y) {
            $temp = $arr1;
            array_push($temp, $y);
            $out[] = $temp;
        }

        return $out;
    }

    /**
     * @param array $bet
     * @param array $win
     * @return array
     */
    public static function prepareResult(array $bet, array $win)
    {
        $out = [];

        foreach ($bet as $key => $item) {

            $eventId = $key + 1;

            if (in_array($eventId, $win)) {
                $out[] = $item;
            }
            else {
                $out[] = array_diff(['1', 'X', '2'], [$item]);
            }
        }

        return $out;
    }

    /**
     * @param array $bet
     * @param array $win
     * @return \Generator
     * @throws \Exception
     */
    public static function fillCombination(array $bet, array $win){

        $needToAdd = count($bet) - count($win);

        if ($needToAdd < 0) {
            throw new \Exception("Illegal arguments");
        }

        if ($needToAdd == 0) {
            yield $bet;
            return;
        }

        $permutation = new RepeatedPermutation(['1', 'X', '2'], $needToAdd);

        foreach ($permutation->generator() as $perm)
        {
            $k = 0;

            $out = [];

            foreach ($bet as $key => $item) {

                $eventId = $key + 1;

                if (in_array($eventId, $win)) {
                    $out[] = $item;
                }
                else {
                    $out[] = $perm[$k++];
                }
            }

            yield $out;
        }
    }

    public static function countMatchValues(array $arr1, array $arr2)
    {
        if (count($arr1) !== count($arr2)) {
            throw new \Exception("Arrays should have equal size");
        }

        $k = 0;

        foreach ($arr1 as $key => $value) {
            if ($value === $arr2[$key]) $k++;
        }

        return $k;
    }

    public static function countMatchResult(array $arr1, array $arr2)
    {
        if (count($arr1) != count($arr2)) {
            throw new \Exception("Arrays should have equal size");
        }

        $k = 0;

        $matchValues = function ($a, $b) {
            if (!is_array($a)) $a = [$a];
            if (!is_array($b)) $b = [$b];

            foreach ($a as $item) {
                if (in_array($item, $b)) return true;
            }

            return false;
        };

        foreach ($arr1 as $key => $value) {
            if ($matchValues($arr1[$key], $arr2[$key])) $k++;
        }

        return $k;
    }

    /**
     * @param array $lines
     * @return array
     * @throws \Exception
     */
    public static function array_combine(array $lines)
    {
        if (!count($lines)) {
            throw new \Exception("Incorrect argument");
        }

        $count = count($lines[0]);

        foreach ($lines as $line) {
            if ($count !== count($line)) {
                throw new \Exception("Arrays should have equal size");
            }
        }

        $firstLine = array_shift($lines);

        foreach ($lines as $line) {
            foreach ($line as $key => $item) {
                foreach ($item as $value) {
                    if (!in_array($value, $firstLine[$key])) {
                        array_push($firstLine[$key], $value);
                    }
                }
            }
        }

        return $firstLine;
    }
}

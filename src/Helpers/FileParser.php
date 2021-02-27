<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 01/03/20
 * Time: 18:49
 */

namespace Helpers;

use \Exception;

class FileParser
{

    /**
     * Prepare arrays for eventsfrom array provider
     *
     * @param string $file
     * @return array
     */
    public static function parseFileWithEvents(string $file) : array
    {
        $lines = explode(PHP_EOL, $file);

        $matches = preg_grep('/(?:pin|preset)$/', $lines);

        if (!$matches) {
            $matches = preg_grep('/\d{1,2}\s{2}\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}/', $lines);
        }

        $eventsAssoc = [];

        $i = 0;

        foreach ($matches as $m) {

            $l = []; $i++;

            preg_match('/\s{4,5}(1?\d)/i', $m, $l);

            $number = (int)$l[1];

            $l = [];

            preg_match('/.+\s-\s.+\s{2,}/U', $m, $l);

            $title = trim(array_shift($l));

            $p = [];

            preg_match('/(0\.\d+)\s+(0\.\d+)\s+(0\.\d+)\s+(\w+)/', $m, $p);

            array_shift($p);

            list($p1, $px, $p2, $source) = $p;

            $eventsAssoc[$number] =  [
                'p1' => $p1,
                'px' => $px,
                'p2' => $p2,
                'title' => $title,
                'source' => $source,
                'number' => $number
            ];
        }

        return $eventsAssoc;
    }

    public static function parseFileWithBets(string $file, string $bookMaker) : array
    {
        $lines = explode(PHP_EOL, $file);

        $keys = array_shift($lines);

        $keys = explode(";", $keys);

        $out = [];

        if ('fonbet' == $bookMaker) {
            $regexp = "/\"(\d{2}\;(\d+\-\([1|X|2]\)[;|\.])+)\"/";
        } elseif ('betcity' == $bookMaker) {
            $regexp = "/\"((?:\d|=|;|X|Х)+)\"/";
        }
        else {
            throw new Exception("Unknown bookmaker $bookMaker");
        }

        foreach ($lines as $line) {

            if ($line) {

                if (preg_match_all($regexp, $line, $bet)) {

                    $betString = $bet[1][0];

                    $parts = explode(";\"$betString\";", $line);

                    $arr = array_merge(explode(";", $parts[0]), [$betString], explode(";", $parts[1]));

                    $items = [];

                    foreach ($arr as $i => $item) {
                        if ($item) $items[$keys[$i]] = $item;
                    }

                    array_push($out, $items);
                };
            }
        }

        return $out;
    }

}
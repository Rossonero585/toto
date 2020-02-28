<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 01/03/20
 * Time: 18:49
 */

namespace Helpers;

class FileParser
{

    /**
     * Prepare arrays for eventsfrom array provider
     *
     * @param string $file
     * @return array
     */
    public function parseFileWithEvents(string $file) : array
    {
        $lines = explode(PHP_EOL, $file);

        $matches = preg_grep('/\d{1,2}\s{2}\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}/', $lines);

        $eventsAssoc = [];

        $i = 0;

        foreach ($matches as $m) {

            $l = []; $i++;

            preg_match('/.+\s\-\s.+\s+/U', $m, $l);

            $title = trim(array_shift($l));

            $p = [];

            preg_match('/(0\.\d{3,})\s{1,}(0\.\d{3,})\s{1,}(0\.\d{3,})\s{1,}(\w{1,})/', $m, $p);

            array_shift($p);

            list($p1, $px, $p2, $source) = $p;

            array_push($eventsAssoc, [
                'p1' => $p1,
                'px' => $px,
                'p2' => $p2,
                'title' => $title,
                'id' => $i,
                'source' => $source
            ]);
        }

        return $eventsAssoc;
    }

}
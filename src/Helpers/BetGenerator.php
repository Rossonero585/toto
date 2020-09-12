<?php

namespace Helpers;

use Models\Event;


class BetGenerator
{
    const MIN_P = 0.285;

    private $eventsHelper;

    public function __construct(EventsHelper $helper)
    {
        $this->eventsHelper = $helper;
    }

    public function generateBets()
    {
        $margins = $this->eventsHelper->getMarginAMatrix(self::MIN_P);

        $maxMargin = $this->getMaxOfArrays($margins);

        $relativeMargins = $this->getRelativeArray($margins, $maxMargin);

        $this->dumpArrayToFile($relativeMargins);

        $probabilities = $this->eventsHelper->getProbabilityMatrix(self::MIN_P);

        $maxP = $this->getMaxOfArrays($probabilities);

        $relativeProbabilities = $this->getRelativeArray($probabilities, $maxP);

        $this->dumpArrayToFile($relativeProbabilities);

        $priorityArray = [];

        foreach ($relativeProbabilities as $i => $eventP) {

            $eventArray = [];

            foreach ($eventP as $j => $p) {
                $eventArray[$j] =  $p + $relativeMargins[$i][$j];
            }

            array_push($priorityArray, $eventArray);
        }

        $this->dumpArrayToFile($probabilities);
        $this->dumpArrayToFile($priorityArray);

        $plainPriorityArray = $this->convertToPlainArray($priorityArray);

        $requiredOutcomes = $this->getPlainArrayOfMaxOfEachSubArray($priorityArray);

        arsort($plainPriorityArray);

        $bestOutcomes = $requiredOutcomes;

        $count = 0;

        foreach ($plainPriorityArray as $key => $outcome) {
            if (!isset($requiredOutcomes[$key])) {
                $bestOutcomes[$key] = $outcome;
                $count++;
            }

            if ($count > 6) break;
        }

        $outcomes = $this->plainArrayToMultiArray($bestOutcomes);

        ksort($outcomes);

        $resultArray = $this->convertMultiArrayToResult($outcomes);

//        var_dump($resultArray);

        $this->dumpArrayToFile($resultArray);

        $comb = ArrayHelper::array_combination($resultArray);

        $this->dumpArrayToFile($comb);


//
//        do {
//
//
//        } while ($i < 10e10);

    }

    private function getMaxOfArrays(array $items) : float
    {
        $max = -1;

        foreach ($items as $subItem) {
            foreach ($subItem as $i) {
                if ($i > $max) {
                    $max = $i;
                }
            }
        }

        return $max;
    }

    private function getPlainArrayOfMaxOfEachSubArray(array $items) : array
    {
        $plainArray = [];

        foreach ($items as $key => $subItem) {
            $multiKey = $key."_";
            $max = -1;
            $maxKey = 0;
            foreach ($subItem as $subKey => $item) {
                if ($item > $max) {
                    $max = $item;
                    $maxKey = $multiKey.$subKey;
                }
            }
            $plainArray[$maxKey] = $max;
        }

        return $plainArray;
    }

    private function getRelativeArray(array $items, $max) : array
    {
        return array_map(function ($subItem) use($max) {
            return array_map(function ($item) use($max) {
                return $item / $max;
            }, $subItem);
        }, $items);
    }

    private function dumpArrayToFile(array $array)
    {
        file_put_contents(ROOT_DIR."/"."generator.txt", PHP_EOL, FILE_APPEND);

        foreach ($array as $item) {
            file_put_contents(ROOT_DIR."/"."generator.txt", implode(" ", $item).PHP_EOL, FILE_APPEND);
        }
    }

    private function convertToPlainArray(array $priorityArray) : array
    {
        $plainArray = [];

        foreach ($priorityArray as $key => $subItem) {
            $multiKey = $key."_";
            foreach ($subItem as $subKey => $value) {
                $subMultiKey = $multiKey.$subKey;
                $plainArray[$subMultiKey] = $value;
            }
        }

        return $plainArray;
    }

    private function plainArrayToMultiArray(array $plainArray) : array
    {
        $out = [];

        foreach ($plainArray as $key => $value) {
            list($i, $j) = explode("_", $key);

            if (!isset($out[$i])) $out[$i] = [];

            $out[$i][$j] = $value;
        }

        return $out;
    }

    private function convertMultiArrayToResult(array $priorityArray)
    {
        return array_map(function (array $item) {
            return array_map(function ($key) {
                return $key;
            }, array_keys($item));
        }, $priorityArray);
    }
}

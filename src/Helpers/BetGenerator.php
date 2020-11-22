<?php

namespace Helpers;

class BetGenerator
{

    private $eventsHelper;

    public function __construct(EventsHelper $helper)
    {
        $this->eventsHelper = $helper;
    }

    public function generateBets()
    {
        $minP = (float)$_ENV['MIN_P'];
        $minAvgP = (float)$_ENV['MIN_AVG_P'];
        $maxDeviation = (float)$_ENV['MAX_D'];
        $maxPackageSize = (int)$_ENV['BETS_COUNT'];

        $matrix = $this->getRelativeMatrix($minP);

        $optimalOutcomes = $this->convertToPlainArray($matrix);

        arsort($optimalOutcomes);

        $requiredOutcomes = $this->getRequiredOutcomes($minP);

        $bestMarginOutcomes = [];

        $output = array_merge($requiredOutcomes, $bestMarginOutcomes);

        $count = 0;

        foreach ($optimalOutcomes as $key => $outcome) {
            if (!isset($output[$key])) {
                $output[$key] = $outcome;
                $count++;
            }

            if ($count > 8) break;
        }

        $outcomes = $this->plainArrayToMultiArray($output);

        ksort($outcomes);

        $resultArray = $this->convertMultiArrayToResult($outcomes);

        $allBets =  ArrayHelper::array_combination($resultArray);

        $i = 0;

        $out = [];

        /** @var array $bet */
        foreach ($allBets as $bet) {
            $p = $this->eventsHelper->getAverageProbability($bet);
            $deviation = $this->eventsHelper->getAverageDeviationOfBet($bet);

            if ($p >= $minAvgP && $deviation >= $maxDeviation) {
                $i++;
                array_push($out, $bet);
            }

            if ($i >= $maxPackageSize) break;
        }

        return $out;
    }

    private function getRelativeMatrix($minP)
    {
        $margins = $this->eventsHelper->getMarginMatrix($minP);

        $maxMargin = $this->getMaxOfArrays($margins);

        $relativeMargins = $this->getRelativeArray($margins, $maxMargin);

        $probabilities = $this->eventsHelper->getProbabilityMatrix($minP);

        $maxP = $this->getMaxOfArrays($probabilities);

        $relativeProbabilities = $this->getRelativeArray($probabilities, $maxP);

        $priorityArray = [];

        foreach ($relativeProbabilities as $i => $eventP) {

            $eventArray = [];

            foreach ($eventP as $j => $p) {
                $eventArray[$j] =  $p + $relativeMargins[$i][$j];
            }

            array_push($priorityArray, $eventArray);
        }

        return $priorityArray;
    }

    private function getBestMarginOutcomes()
    {
        $margins = $this->eventsHelper->getMarginMatrix(0);

        $plainArray = $this->convertToPlainArray($margins);

        arsort($plainArray);

        return array_slice($plainArray, 0,4);
    }

    private function getRequiredOutcomes($minP) : array
    {
        $items = $this->getRelativeMatrix($minP);

        $plainArray = [];

        foreach ($items as $key => $subItem) {
            $multiKey = $key."_";
            $max = -10e10;
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

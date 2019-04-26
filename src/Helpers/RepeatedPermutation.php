<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 06/11/17
 * Time: 16:55
 */

namespace Helpers;

class RepeatedPermutation
{
    /** @var array */
    private $dataSet;

    /** @var int */
    private $length;

    public function __construct(array $dataSet, int $length)
    {
        $this->dataSet = $dataSet;
        $this->length = $length;
    }

    public function setLength(int $length)
    {
        $this->length = $length;
    }

    public function generator()
    {
        return $this->recGenerator($this->dataSet, $this->length, []);
    }

    private function recGenerator(array $dataSet, int $length, array $temp)
    {
        if ($length <= 0) {
            throw new \Exception("Illegal argument");
        }
        $i = count($temp);

        for ($j = 0; $j < count($dataSet); $j++)
        {
            $temp[$i] = $dataSet[$j];

            if (count($temp) == $length) {
                yield $temp;
            }
            else {
                yield from $this->recGenerator($dataSet, $length, $temp);
            }
        }
    }
}
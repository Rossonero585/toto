<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 14/10/17
 * Time: 23:57
 */
namespace Tests\Helpers;

use Helpers\ArrayHelper;
use PHPUnit\Framework\TestCase;

class ArrayHelperTest extends TestCase
{
    public function testArrayCombination()
    {
        $arr = ArrayHelper::array_combination([[1], [1, 'x', 2]]);

        $this->assertEquals(3, count($arr));

        $this->assertEquals(
            [
                [1,1],
                [1,'x'],
                [1,2]
            ],
            $arr
        );
    }

    public function testPrepareResult()
    {
        $arr = ArrayHelper::prepareResult([1, 'X', 2, 'X', 1], [1,2,5]);

        $this->assertEquals(1, $arr[0]);
        $this->assertEquals('X', $arr[1]);
        $this->assertTrue($this->areArraysSimilar([1, 'X'], $arr[2]));
        $this->assertTrue($this->areArraysSimilar([2, 1], $arr[3]));
        $this->assertEquals(1, $arr[4]);
    }

    public function testFillCombination()
    {
        $count = 0;

        $out = [];

        foreach (ArrayHelper::fillCombination([1, 2, 1, 1], [1,3]) as $item) {
            $key = implode(",", $item);
            $this->assertTrue(!isset($out[$key]));
            $out[$key] = $item;
            $count++;
        }

        $this->assertEquals(9, $count);
    }

    public function testCombineArrays()
    {
        $testedArr = [
            [[1], [2], ['x']],
            [['x'], [2], ['x']],
            [[2], [1], ['x']],
        ];

        $outArr = ArrayHelper::array_combine($testedArr);

        $this->assertTrue($this->areArraysSimilar(
            [1,'x',2],
           $outArr[0]
        ));

        $this->assertTrue($this->areArraysSimilar(
            [2,1],
            $outArr[1]
        ));

        $this->assertTrue($this->areArraysSimilar(
            ['x'],
            $outArr[2]
        ));
    }

    public function areArraysSimilar(array $arr1, array $arr2)
    {
        return count(array_diff($arr1, $arr2)) === 0;
    }

    public function testCountMatch()
    {
        $a = [1, 2, 3];

        $b = [1, 2, 0];

        $countMatch = ArrayHelper::countMatchResult($a, $b);

        $this->assertEquals(2, $countMatch);
    }

    public function testFonBetFormat()
    {
        $bet = str_split("2X12X2X21XX1XX2");

        $drawMask = ArrayHelper::convertToFonBetFormat($bet, 'X');
        $win1Mask = ArrayHelper::convertToFonBetFormat($bet, '1');
        $win2Mask = ArrayHelper::convertToFonBetFormat($bet, '2');

        $this->assertEquals(13906, $drawMask);
        $this->assertEquals(2308, $win1Mask);
        $this->assertEquals(16553, $win2Mask);
    }
}
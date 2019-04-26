<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 12/11/17
 * Time: 13:27
 */

namespace Tests\Helpers;

use Helpers\RepeatedPermutation;
use PHPUnit\Framework\TestCase;

class RepeatedPermutationTest extends TestCase
{
    public function testPermutation()
    {
        $instance = new RepeatedPermutation(['1', 'x', '2'], 4);

        $count = 0;

        $resultArr = [];

        foreach ($instance->generator() as $item) {
            $key = implode(",", $item);

            $this->assertTrue(!isset($resultArr[$key]));

            $resultArr[implode(",", $item)] = $item;
            $count++;
        }

        $this->assertEquals(pow(3,4), $count);

    }
}

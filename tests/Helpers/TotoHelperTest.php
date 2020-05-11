<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 22/10/17
 * Time: 13:13
 */

namespace Tests\Helpers;

use Helpers\RepeatedPermutation;
use Helpers\TotoHelper;
use Models\BreakDown;
use Models\BreakDownItem;
use Models\Toto;
use PHPUnit\Framework\TestCase;

class TotoHelperTest extends TestCase
{
    /** @var  TotoHelper */
    private $totoHelper;

    /** @var  BreakDown */
    private $breakDown;

    protected function setUp()
    {
        $toto = new Toto(
            new \DateTime(),
            1000000,
            500000,
            14,
            [9 => 0.3, 10 => 0.2, 11 => 0.15, 12 => 0.1, 13 => 0.1, 14 => 0.05],
            'betcity'
        );

        $breakDowns = [];

        array_push($breakDowns, new BreakDownItem(
            9,
            30000
        ));

        array_push($breakDowns, new BreakDownItem(
            10,
            20000
        ));

        array_push($breakDowns, new BreakDownItem(
            11,
            10000
        ));

        array_push($breakDowns, new BreakDownItem(
            12,
            5000
        ));

        array_push($breakDowns, new BreakDownItem(
            13,
            200
        ));

        array_push($breakDowns, new BreakDownItem(
            14,
            100
        ));

        $this->breakDown = new BreakDown($breakDowns);

        $this->totoHelper = new TotoHelper($toto,0);
    }

    public function testGetRatioByWinCount()
    {
        $breakDown = $this->breakDown;

        $ratio = $this->totoHelper->getRatioByWinCount(9, $breakDown);

        $this->assertEquals(0.3*1000000 / 30000, $ratio);

        $ratio = $this->totoHelper->getRatioByWinCount(10, $breakDown);

        $this->assertEquals((0.3*1000000 / 30000 + 0.2*1000000 / 20000), $ratio);

        $ratio = $this->totoHelper->getRatioByWinCount(11, $breakDown);

        $this->assertEquals((0.3*1000000 / 30000 + 0.2*1000000 / 20000 + 0.15*1000000 / 10000), $ratio);

        $ratio = $this->totoHelper->getRatioByWinCount(14, $breakDown);

        $this->assertEquals(
            (
                0.3*1000000 / 30000 +
                0.2*1000000 / 20000 +
                0.15*1000000 / 10000 +
                0.1*1000000 / 5000 +
                0.1*1000000 / 200 +
                0.05*1000000 / 100 +
                500000 / 100
            ),
        $ratio);

    }

    public function testOutcomeIteration()
    {
        $count = 0;

        $bet = [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1];

        foreach ($this->totoHelper->iterateWinnerCombinations($bet) as $c) {
            $count++;
        }

        $expected = $this->calculateTotoComb(14);


        $this->assertEquals($expected, $count);
    }

    private function calculateCombinations(int $n, int $k)
    {
        if ($n < $k) throw new \Exception("Illegal argument");
        $c = 1;

        for ($i = 1; $i <= $n; $i++) {
            $c = $c * $i;
        }

        for ($i = 1; $i <= $n - $k; $i++) {
            $c = $c / $i;
        }

        for ($i = 1; $i <= $k; $i++) {
            $c = $c / $i;
        }

        return $c;
    }

    private function calculateTotoComb(int $count)
    {
        $c = 0;

        $bet = [];

        for ($i = 0; $i < $count; $i++) {
            $bet[$i] = '1';
        }

        $permutation = new RepeatedPermutation(['1', 'x', '2'], $count);

        foreach ($permutation->generator() as $item) {
            if ($this->countMatches($bet, $item) > 8) $c++;
        }

        return $c;
    }

    private function countMatches(array $bet, array $outcome)
    {
        if (count($bet) != count($outcome)) {
            throw new \Exception("Illegal arguments");
        }

        $count = 0;

        foreach ($bet as $key => $value) {
            if ($value == $outcome[$key]) {
                $count++;
            }
        }

        return $count;
    }
}
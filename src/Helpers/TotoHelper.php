<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 21/10/17
 * Time: 22:28
 */

namespace Helpers;

use drupol\phpermutations\Generators\Combinations;
use Models\BreakDown;
use Models\Toto;

class TotoHelper
{
    /** @var  Toto */
    private $toto;

    /** @var  float */
    private $bet;

    public function __construct(Toto $toto, float $bet)
    {
        $this->toto = $toto;
        $this->bet = $bet;
    }

    public function getRatioByWinCount(int $count, BreakDown $breakDown)
    {
        $winCounts = array_keys($this->toto->getWinnerCounts());

        $maxWinCount = max($winCounts);

        $minWinCount = min($winCounts);

        if ($count <= 0 || $count > $maxWinCount) {
            throw new \Exception("The count of events is out of range");
        }

        if (!in_array($count, $winCounts)) {
            return 0;
        }

        $ratio = 0;

        do {
            $breakDownItem = $breakDown->getBreakDownItem($count);

            $betPot = $breakDownItem ? $breakDownItem->getPot() : 0;

            $pot = $this->getPotForWinCount($count);

            $ratio = $ratio + $pot / ($betPot + $this->bet);

            $count--;
        }
        while ($count >= $minWinCount);

        return $ratio;
    }

    public function iterateWinnerCombinations(array $bet)
    {
        if (count($bet) != $this->toto->getEventCount()) {
            throw new \Exception("Count of events doesn't match toto");
        }

        $winnerCounts = array_keys($this->toto->getWinnerCounts());

        $range = range(1, $this->toto->getEventCount());

        $winnerCounts = array_reverse($winnerCounts);

        $map = [];

        foreach ($winnerCounts as $count) {
            $combinations = new Combinations($range, $count);
            foreach ($combinations->generator() as $combination) {
                foreach (ArrayHelper::fillCombination($bet, $combination) as $betItem) {
                    $key = implode(",", $betItem);
                    if (!isset($map[$key])) {
                        $map[$key] = 1;
                        yield $count => $betItem;
                    }
                }
            }
        }
    }

    private function getPotForWinCount(int $count)
    {
        $winCounts = $this->toto->getWinnerCounts();

        if (!in_array($count, array_keys($winCounts))) {
            return 0;
        }

        $pot = $winCounts[$count] * ($this->toto->getPot() + $this->bet);

        if ($this->toto->getEventCount() == $count) {
            $pot = $pot + $this->toto->getJackPot();
        }

        return $pot;
    }
}
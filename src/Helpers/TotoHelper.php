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
use \Exception;

class TotoHelper
{
    /** @var  Toto */
    private $toto;

    /** @var  float */
    private $bet;

    private static $jsonStorage = [];

    public function __construct(Toto $toto, float $bet)
    {
        $this->toto = $toto;
        $this->bet = $bet;
    }

    public function getRatioByWinCount(int $count, BreakDown $breakDown, $includeBet = true, $includeJackPot = false)
    {
        $winCounts = array_keys($this->toto->getWinnerCounts());

        $maxWinCount = max($winCounts);

        $minWinCount = min($winCounts);

        if ($count <= 0 || $count > $maxWinCount) {
            throw new Exception("The count of events is out of range");
        }

        if (!in_array($count, $winCounts)) {
            return 0;
        }

        $ratio = 0;

        do {
            $breakDownItem = $breakDown->getBreakDownItem($count);

            $betPot = $breakDownItem ? $breakDownItem->getPot() : 0;

            $pot = $this->getPotForWinCount($count, $includeJackPot);

            $ratio = $ratio + $pot / ($includeBet ? $this->bet + $betPot : $betPot);

            $count--;
        }
        while ($count >= $minWinCount);

        return $ratio;
    }

    public function getRatioByCategory(int $cat, BreakDown $breakDown)
    {
        $winCounts = array_keys($this->toto->getWinnerCounts());

        $maxWinCount = max($winCounts);

        if ($cat <= 0 || $cat > $maxWinCount) {
            throw new Exception("The count of events is out of range");
        }

        if (!in_array($cat, $winCounts)) {
            return 0;
        }

        $breakDownItem = $breakDown->getBreakDownItem($cat);

        $betPot = $breakDownItem ? $breakDownItem->getPot() : 0;

        $pot = $this->getPotForWinCount($cat, true);

        return ($pot + $this->bet) / ($betPot + $this->bet);

    }

    public function iterateWinnerCombinations(array $bet)
    {
        if (count($bet) != $this->toto->getEventCount()) {
            throw new Exception("Count of events doesn't match toto");
        }

        $winnerCounts = array_keys($this->toto->getWinnerCounts());

        $minWinCount = min($winnerCounts);

        $range = range(1, $this->toto->getEventCount());

        $combinations = new Combinations($range, $minWinCount);

        foreach ($combinations->generator() as $combination) {
            foreach (ArrayHelper::fillCombination($bet, $combination) as $betItem) {
                $key = implode(",", $betItem);
                if (!isset($map[$key])) {
                    $map[$key] = 1;
                    yield $betItem;
                }
            }
        }
    }

    private function getPotForWinCount(int $count, bool $includeJackPot)
    {
        $winCounts = $this->toto->getWinnerCounts();

        if (!in_array($count, array_keys($winCounts))) {
            return 0;
        }

        $pot = $winCounts[$count] * ($this->toto->getPot() + $this->bet);

        if ($includeJackPot && $this->toto->getEventCount() == $count) {
            $pot = $pot + $this->toto->getJackPot();
        }

        return $pot;
    }

    /**
     * @param $totoId
     * @return \stdClass
     */
    public static function getJsonToto($totoId)
    {
        $requestedUrl = $_ENV['BET_CITY_URL']."/d/se/one?id=$totoId";

        if (!isset(self::$jsonStorage[$requestedUrl])) {
            self::$jsonStorage[$requestedUrl] = json_decode(file_get_contents($requestedUrl));
        }

        return self::$jsonStorage[$requestedUrl];
    }
}
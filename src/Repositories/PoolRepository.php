<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 21/10/17
 * Time: 17:39
 */

namespace Repositories;
use Builders\BreakDownBuilder;
use Helpers\ArrayHelper;
use Models\Bet;
use Models\BreakDown;
use Utils\Pdo;

class PoolRepository extends Repository
{
    /** @var  BreakDown[] */
    private $breakDowns = [];

    /** @var  array */
    private $pool;

    public function insertFromFile($str)
    {
        $sql = <<<EOD
INSERT IGNORE INTO pool
          (code, money, r1, r2, r3, r4, r5, r6, r7, r8, r9, r10, r11, r12, r13, r14)
VALUES
EOD;
        $lines = explode("\n", $str);

        foreach ($lines as $line) {
            $items = explode("|", $line);

            if (count($items) > 1) {
                $code = $items[0];
                $money = (float)trim(substr($items[2], 0, -3));

                $results = explode(";", $items[3]);

                array_pop($results);

                if (preg_match("/,/", $items[3])) {
                    $results = array_map(function($r) {return explode(",", $r);}, $results);
                    $results = ArrayHelper::array_combination($results);

                    $money = $money / count($results);

                    foreach ($results as $key => $subResult) {
                        $tempCode = $code.$key;
                        $sql .= $this->addSqlRow($money, $tempCode, $subResult);
                    }
                }
                else {
                    $sql .= $this->addSqlRow($money, $code, $results);
                }
            }
        }

        $sql = substr($sql, 0, -2);

        $st = $this->pdo->prepare($sql);

        $st->execute();
    }


    public function getPoolItem(array $bet)
    {
        $sql = <<<EOD
SELECT * FROM pool WHERE 
r1 = :r1 AND  
r2 = :r2 AND  
r3 = :r3 AND  
r4 = :r4 AND  
r5 = :r5 AND  
r6 = :r6 AND  
r7 = :r7 AND  
r8 = :r8 AND  
r9 = :r9 AND  
r10 = :r10 AND  
r11 = :r11 AND  
r12 = :r12 AND  
r13 = :r13 AND  
r14 = :r14
EOD;
        $st = $this->getCachedStatement($sql);

        $counter = 0;

        foreach ($bet as $key => $item) {
            ++$counter;
            $st->bindParam("r".$counter, $bet[$key]);
        }

        $st->execute();

        $items = $st->fetch();

        return $items ? new Bet(0, (float)$items['money'], $bet) : null;
    }

    private function addSqlRow($money, $code, $results)
    {
        $row = <<<EOT
'$code', $money
EOT;

        foreach ($results as $r) {
            $row .= " ,'$r'";
        }

        $row = " ($row), ";

        return $row;
    }

    /**
     * @param array $results
     * @return BreakDown|null
     */
    private function getCachedBreakDown(array $results)
    {
        $key = md5(json_encode($results));

        if (isset($this->breakDowns[$key])) {
            return $this->breakDowns[$key];
        }

        return null;
    }

    private function addCachedBreakDown(array $results, BreakDown $breakDown)
    {
        $key = md5(json_encode($results));

        $this->breakDowns[$key] = $breakDown;

        return $this;
    }

    /**
     * @param array $results
     * @return \Models\BreakDown
     */
    public function getWinnersBreakDown(array $results)
    {
        $cachedBreakDown = $this->getCachedBreakDown($results);

        if ($cachedBreakDown) return $cachedBreakDown;

        $ifBlock = "";
        $i = 0;

        foreach ($results as $item) {
            $i++;
            if (is_array($item)) {
                $inBlock = "";
                foreach ($item as $k) {
                    $inBlock .= "'$k',";
                }
                $inBlock = substr($inBlock, 0, -1);
                $ifBlock.= "IF (r$i IN ($inBlock), 1, 0) + ";
            }
            else {
                $ifBlock.= "IF (r$i = '$item', 1, 0) + ";
            }

        }

        $ifBlock = substr($ifBlock, 0, -3);

        $query =
            <<<EOD
SELECT
	(
      {$ifBlock}
    ) AS amount, COUNT(*) AS win_comb, SUM(money) AS pot
FROM `pool` GROUP BY amount ORDER BY amount DESC
EOD;

        $st = $this->pdo->query($query);

        $arr = $st->fetchAll();

        $breakDown = BreakDownBuilder::createBreakDownFromArray($arr);

        $this->addCachedBreakDown($results, $breakDown);

        return $breakDown;
    }


    /**
     * @param array $results
     * @return BreakDown|null
     * @throws \Exception
     */
    public function getWinnersBreakDownUsingArray(array $results)
    {
        $cachedBreakDown = $this->getCachedBreakDown($results);

        if ($cachedBreakDown) return $cachedBreakDown;

        $pool = $this->getAllPool();

        $outArray = [];

        foreach ($pool as $poolItem) {
            $money = (float)$poolItem['money'];
            unset($poolItem['money'], $poolItem['code']);

            $matched = ArrayHelper::countMatchResult($results, array_values($poolItem));

            if (!isset($outArray[$matched])) {
                $outArray[$matched] = [
                    'amount' => $matched,
                    'pot' => 0
                ];
            }

            $outArray[$matched]['pot'] += $money;
        }

        $breakDown = BreakDownBuilder::createBreakDownFromArray($outArray);

        $this->addCachedBreakDown($results, $breakDown);

        return $breakDown;
    }


    /**
     * @return array
     */
    public function getAllPool()
    {
        if (!$this->pool) {

            $sql = <<<EOD
SELECT * FROM pool 
EOD;
            $st = $this->getCachedStatement($sql);

            $st->execute();

            $this->pool = $st->fetchAll();
        }

        return $this->pool;
    }


}
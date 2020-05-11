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
use Models\BreakDown;

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
          (code, money, result, toto_id)
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
SELECT * FROM pool WHERE result = :result AND toto_id = :toto_id
EOD;
        $st = $this->getCachedStatement($sql);

        $st->execute([
            'result' => implode("", $bet),
            'toto_id' => $this->getTotoId()
        ]);

        $items = $st->fetch();

        return (bool)count($items);
    }

    private function addSqlRow($money, $code, $results)
    {
        $results = implode("", $results);

        $totoId = $this->getTotoId();

        return " ('$code', $money, '$results', $totoId), ";
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
     * @return BreakDown|null
     * @throws \Exception
     */
    public function getWinnersBreakDown(array $results)
    {
        $cachedBreakDown = $this->getCachedBreakDown($results);

        if ($cachedBreakDown) return $cachedBreakDown;

        $pool = $this->getAllPool();

        $outArray = [];

        foreach ($pool as $poolItem) {
            $money = (float)$poolItem['money'];

            $matched = ArrayHelper::countMatchResult($results, str_split($poolItem['result']));

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
SELECT * FROM pool WHERE toto_id = :toto_id
EOD;
            $st = $this->getCachedStatement($sql);

            $st->execute([
                'toto_id' => $this->getTotoId()
            ]);

            $this->pool = $st->fetchAll();
        }

        return $this->pool;
    }


}
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

class PoolRepository extends Repository
{
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

    /**
     * @param array $results
     * @return \Models\BreakDown
     */
    public function getWinnersBreakDown(array $results)
    {
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

        return $breakDown;
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


}
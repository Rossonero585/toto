<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 12/11/17
 * Time: 20:44
 */
namespace Repositories;

class PreparedResultRepository extends Repository
{


    public function insertCombinations($combGenerator, array $bet)
    {
        $sql = <<<EOD
REPLACE INTO prepared_result
          (bet, hash, winCount, r1, r2, r3, r4, r5, r6, r7, r8, r9, r10, r11, r12, r13, r14)
VALUES
EOD;

        $c = 0;
        foreach ($combGenerator as $winCount => $item) {
            $c++;
            $betHash = implode("", $bet);
            $itemHash = implode("", $item);

            $results = "";

            foreach ($item as $r) {
                $results .= " '$r', ";
            }

            $results = substr($results, 0, -2);

            $sql .= " ('$betHash', '$itemHash', $winCount, $results), ";
        }

        $sql = substr($sql, 0, -2);


        $st = $this->pdo->prepare($sql);

        $st->execute();
    }


    public function getAllPool()
    {
        $query = <<<EOD
SELECT * FROM prepared_result
EOD;

        $st= $this->getCachedStatement($query);

        return $st;
    }


    public function updateBreakDown(array $requestToUpdate)
    {
        if (!count($requestToUpdate)) {
            return;
        }

        $query = <<<EOD
INSERT INTO prepared_result 
  (r1, r2, r3, r4, r5, r6, r7, r8, r9, r10, r11, r12, r13, r14, bet, winCount, hash, id, break_down) 
VALUES
EOD;
        foreach ($requestToUpdate as $key => $item) {
            $query .= implode(', ', $item);
        }

        $query = substr($query, 0, -2);

        $query .= " ON DUPLICATE KEY UPDATE break_down = VALUES(break_down)";

        $this->pdo->exec($query);
    }

}
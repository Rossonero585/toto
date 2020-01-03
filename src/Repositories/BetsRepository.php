<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 05/05/19
 * Time: 22:30
 */

namespace Repositories;


use Models\Bet;

class BetsRepository extends Repository
{

    public function getBetItemById(int $id)
    {
        $sql = <<<EOD
SELECT * FROM bet_items bi WHERE bi.id = :id
EOD;

        $st = $this->getCachedStatement($sql);

        $st->execute(['id' => $id]);

        $betArr = $st->fetchAll(\PDO::FETCH_ASSOC);

        if (!count($betArr)) throw new \Exception("Bet item with $id is not found");

        return $this->createBet(array_shift($betArr));
    }


    /**
     * @return array
     */
    public function getAllPackages()
    {
        $sql = <<<EOD
SELECT * FROM bets 
EOD;
        $st = $this->pdo->prepare($sql);

        $st->execute();

        return $st->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param int $id
     * @return Bet[]
     */
    public function geBetsOfPackage(int $id)
    {
        $sql = <<<EOD
SELECT bi.id as id, bi.money as money, bi.bet as bet, bi.ev as ev FROM bets b
LEFT JOIN bet_items bi ON bi.bet_id = b.id
WHERE b.id = :id
EOD;

        $st = $this->pdo->prepare($sql);

        $st->execute(['id' => $id]);

        $betArr = $st->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function ($arr) {
            return $this->createBet($arr);
        }, $betArr);

    }


    public function updateBetItemEv(int $id, float $ev = null, float $p = null)
    {
        return $this->updateFieldsById('bet_items', $id, [
            'ev' => $ev,
            'probability' => $p
        ]);
    }

    public function updateBetItemIncome(int $id, int $countMatch, float $income)
    {
        return $this->updateFieldsById('bet_items', $id, [
            'count_match' => $countMatch,
            'income' => $income
        ]);
    }

    /**
     * @param int $id
     * @param float $ev
     * @param float $p
     * @return bool
     */
    public function updateBetEv(int $id, float $ev = null, float $p = null)
    {
        $sql = <<<EOD
UPDATE bets
SET ev = :ev,
    probability = :p
WHERE id =:id
EOD;

        $st = $this->getCachedStatement($sql);

        return $st->execute(['id' => $id, 'ev' => $ev, 'p' => $p]);
    }


    private function createBet(array $arr)
    {
        return new Bet(
            (int)$arr['id'],
            (float)$arr['money'],
            (float)$arr['ev'],
            str_split($arr['bet'])
        );
    }
}
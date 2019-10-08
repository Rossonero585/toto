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
SELECT bi.id as id, bi.money as money, bi.bet as bet FROM bets b
LEFT JOIN bet_items bi ON bi.bet_id = b.id
WHERE b.id = :id
EOD;

        $st = $this->pdo->prepare($sql);

        $st->execute(['id' => $id]);

        $betArr = $st->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function ($arr) {
            return new Bet(
                (int)$arr['id'],
                (float)$arr['money'],
                str_split($arr['bet'])
            );
        }, $betArr);

    }


    public function updateBetItemEv(int $id, float $ev = null, float $p = null)
    {
        $sql = <<<EOD
UPDATE bet_items
SET ev = :ev,
    probability = :p
WHERE id =:id
EOD;

        $st = $this->getCachedStatement($sql);

        return $st->execute(['id' => $id, 'ev' => $ev, 'p' => $p]);
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
}
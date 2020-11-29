<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 05/05/19
 * Time: 22:30
 */

namespace Repositories;


use Models\Bet;
use Models\Input\Bet as InputBet;

class BetItemRepository extends Repository
{
    const TABLE_NAME = 'bet_items';

    public function getBetItemById(int $id)
    {
        $tableName = self::TABLE_NAME;

        $sql = <<<EOD
SELECT * FROM {$tableName} bi WHERE bi.id = :id
EOD;

        $st = $this->getCachedStatement($sql);

        $st->execute(['id' => $id]);

        $betArr = $st->fetchAll(\PDO::FETCH_ASSOC);

        if (!count($betArr)) throw new \Exception("Bet item with $id is not found");

        return $this->createBet(array_shift($betArr));
    }

    /**
     * @param int $id
     * @return Bet[]
     */
    public function geBetsOfPackage(int $id)
    {
        $tableName = self::TABLE_NAME;

        $sql = <<<EOD
SELECT bi.id as id, bi.money as money, bi.bet as bet, bi.ev as ev FROM bets b
LEFT JOIN {$tableName} bi ON bi.bet_id = b.id
WHERE b.id = :id ORDER BY bi.id
EOD;

        $st = $this->getCachedStatement($sql);

        $st->execute(['id' => $id]);

        $betArr = $st->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function ($arr) {
            return $this->createBet($arr);
        }, $betArr);

    }

    /**
     * @param int $id
     * @return Bet[]
     */
    public function getBetsOfPackageWithNullEv(int $id)
    {
        $tableName = self::TABLE_NAME;

        $sql = <<<EOD
SELECT bi.id as id, bi.money as money, bi.bet as bet, bi.ev as ev FROM bets b
LEFT JOIN {$tableName} bi ON bi.bet_id = b.id
WHERE b.id = :id AND bi.ev IS NULL ORDER BY bi.id
EOD;

        $st = $this->getCachedStatement($sql);

        $st->execute(['id' => $id]);

        $betArr = $st->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function ($arr) {
            return $this->createBet($arr);
        }, $betArr);
    }

    /**
     * @param int $id
     * @return Bet[]
     */
    public function getBetsOfPackageWithNotNullEv(int $id)
    {
        $tableName = self::TABLE_NAME;

        $sql = <<<EOD
SELECT bi.id as id, bi.money as money, bi.bet as bet, bi.ev as ev FROM bets b
LEFT JOIN {$tableName} bi ON bi.bet_id = b.id
WHERE b.id = :id AND bi.ev IS NOT NULL ORDER BY bi.id
EOD;

        $st = $this->getCachedStatement($sql);

        $st->execute(['id' => $id]);

        $betArr = $st->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function ($arr) {
            return $this->createBet($arr);
        }, $betArr);
    }

    /**
     * @param InputBet $bet
     * @param int $packageId
     * @return string
     */
    public function addBetItem(InputBet $bet, int $packageId)
    {
        $tableName = self::TABLE_NAME;

        $sql = <<<EOD
INSERT INTO {$tableName} (bet_id, bet, money)
VALUES (:bet_id, :bet, :money)
EOD;

        $st = $this->getCachedStatement($sql);

        $st->execute([
            'bet_id' => $packageId,
            'bet' => implode("", $bet->getResults()),
            'money' => $bet->getMoney()
        ]);

        return $this->pdo->lastInsertId();
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

    public function updateBetItemDev(int $id, float $p, float $d)
    {
        return $this->updateFieldsById('bet_items', $id, [
            'avg_p' => $p,
            'deviation' => $d
        ]);
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
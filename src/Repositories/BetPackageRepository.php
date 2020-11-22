<?php

namespace Repositories;

use Models\BetPackage;

class BetPackageRepository extends Repository
{
    const TABLE_NAME = 'bets';

    /**
     * @param float $money
     * @param \DateTime $dateTime
     * @param boolean $isTest
     * @return BetPackage
     */
    public function addNewPackage(float $money, \DateTime $dateTime, $isTest = false)
    {
        $tableName = self::TABLE_NAME;

        $sql = <<<EOD
INSERT INTO {$tableName} (money, bet_time, toto_id, is_test)
VALUES (:money, :bet_time, :toto_id, :is_test)
EOD;
        $st = $this->getCachedStatement($sql);

        $st->execute([
            'money' => $money,
            'bet_time' => $dateTime->format("Y-m-d H:i:s"),
            'toto_id' => $this->getTotoId(),
            'is_test' => $isTest ? 1 : 0
        ]);

        return new BetPackage(
            $this->pdo->lastInsertId(),
            $money,
            null,
            null,
            null,
            $isTest
        );
    }

    /**
     * @return BetPackage[]
     */
    public function getAllPackages()
    {
        $tableName = self::TABLE_NAME;

        $sql = <<<EOD
SELECT * FROM {$tableName} WHERE toto_id = :toto_id
EOD;
        $st = $this->getCachedStatement($sql);

        $st->execute([
            'toto_id' => $this->getTotoId()
        ]);

        $arr = $st->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function ($arr) {
            return new BetPackage(
                $arr['id'],
                $arr['money'],
                $arr['p'],
                $arr['ev'],
                $arr['income'],
                (bool)$arr['is_test']
            );
        }, $arr);
    }

    /**
     * @param int $id
     * @param float $ev
     * @param float $p
     * @return bool
     */
    public function updateBetEv(int $id, float $ev = null, float $p = null)
    {
        $tableName = self::TABLE_NAME;

        $sql = <<<EOD
UPDATE {$tableName}
SET ev = :ev,
    p = :p
WHERE id =:id
EOD;
        $st = $this->getCachedStatement($sql);

        return $st->execute(['id' => $id, 'ev' => $ev, 'p' => $p]);
    }



}
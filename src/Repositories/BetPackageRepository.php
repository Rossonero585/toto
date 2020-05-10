<?php

namespace Repositories;

use Models\BetPackage;

class BetPackageRepository extends Repository
{
    const TABLE_NAME = 'bets';

    /**
     * @param float $money
     * @param \DateTime $dateTime
     * @return BetPackage
     */
    public function addNewPackage(float $money, \DateTime $dateTime)
    {
        $tableName = self::TABLE_NAME;

        $sql = <<<EOD
INSERT INTO {$tableName} (money, bet_time)
VALUES (:money, :bet_time)
EOD;
        $st = $this->pdo->prepare($sql);

        $st->execute([
            'money' => $money,
            'bet_time' => $dateTime->format("Y-m-d H:i:s")
        ]);

        return new BetPackage(
            $this->pdo->lastInsertId(),
            $money,
            null,
            null,
            null
        );
    }

    /**
     * @return BetPackage[]
     */
    public function getAllPackages()
    {
        $tableName = self::TABLE_NAME;

        $sql = <<<EOD
SELECT * FROM {$tableName} 
EOD;
        $st = $this->pdo->prepare($sql);

        $st->execute();

        $arr = $st->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function ($arr) {
            return new BetPackage(
                $arr['id'],
                $arr['money'],
                $arr['probability'],
                $arr['ev'],
                $arr['income']
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
    probability = :p
WHERE id =:id
EOD;
        $st = $this->getCachedStatement($sql);

        return $st->execute(['id' => $id, 'ev' => $ev, 'p' => $p]);
    }



}
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
    public function insertFromBetsFile($content, $probability, $expectedEv)
    {
        $lines = explode(PHP_EOL, $content);

        $sum = 0;

        $bets = array_map(
            function($l) use(&$sum) {

                $items = explode(";", $l);

                $money = (float)array_shift($items);

                $sum += $money;

                array_pop($items);

                $tempArr = [];

                $tempArr['sum'] = $money;

                $bet = implode("", array_map(function($item) {
                    return explode("=", $item)[1];
                }, $items));

                $tempArr['bet'] = $bet;

                return $tempArr;
            },
            $lines
        );


        $sql = <<<EOD
INSERT IGNORE INTO bets
          (money, probability, expected_ev, income, bet_time)
VALUES (:money, :probability, :expected_ev, :income, :bet_time)
EOD;
        $st = $this->pdo->prepare($sql);

        $st->execute([
            "money" => $sum,
            "probability" => $probability,
            "expected_ev" => $expectedEv,
            "income" => 0,
            "bet_time" => (new \DateTime())->format("Y-m-d H:i:s")
        ]);

        $betId = $this->pdo->lastInsertId();

        $sql = <<<EOD
INSERT IGNORE INTO bet_items
          (bet_id, bet, money)
VALUES (:bet_id, :bet, :money)
EOD;

        $st = $this->pdo->prepare($sql);

        foreach ($bets as $b) {
            $st->execute([
                'bet' => $b['bet'],
                'money' => $b['sum'],
                'bet_id' => $betId
            ]);
        }
    }

    /**
     * @param int $id
     * @return Bet[]
     */
    public function geBetsOfPackage(int $id)
    {
        $sql = <<<EOD
SELECT * FROM bets b
LEFT JOIN bet_items bi ON bi.bet_id = b.id
WHERE b.id = :id
EOD;

        $st = $this->pdo->prepare($sql);

        $st->execute(['id' => $id]);

        $betArr = $st->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function ($arr) {
            return new Bet(
                (float)$arr['money'],
                str_split($arr['bet'])
            );
        }, $betArr);

    }


    /**
     * @param int $id
     * @param float $ev
     * @return bool
     */
    public function updateLastBetEv(int $id, float $ev)
    {
        $sql = <<<EOD
UPDATE bets
SET last_bet_ev = :ev
WHERE id =:id
EOD;

        $st = $this->pdo->prepare($sql);

        return $st->execute(['id' => $id, 'ev' => $ev]);
    }
}
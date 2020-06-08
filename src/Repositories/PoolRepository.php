<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 21/10/17
 * Time: 17:39
 */

namespace Repositories;
use Helpers\ArrayHelper;

class PoolRepository extends Repository
{
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

        return " ('$code', $money, '$results', '$totoId'), ";
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
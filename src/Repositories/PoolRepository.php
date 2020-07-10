<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 21/10/17
 * Time: 17:39
 */

namespace Repositories;
use Builders\Providers\PoolProviderInterface;
use Models\PoolItem;

class PoolRepository extends Repository
{
    /** @var  array */
    private $pool;

    public function insertFromProvider(PoolProviderInterface $poolProvider)
    {
        $s = "INSERT IGNORE INTO pool (result, money, code, bet_time, toto_id) VALUES ";

        /** @var PoolItem $poolItem */
        foreach ($poolProvider->getPoolItem() as $poolItem) {

            $result = $poolItem->getResult();
            $money  = $poolItem->getMoney();
            $code   = $poolItem->getCode();
            $betTime = $poolItem->getBetDate() ? $poolItem->getBetDate()->format('Y-m-d H:i:s') : '';
            $totoId  = $this->getTotoId();

            if ($betTime) {
                $s .= " ('$result', $money, '$code', '$betTime', '$totoId'), ";
            }
            else {
                $s .= " ('$result', $money, '$code', NULL, '$totoId'), ";
            }
        }

        $sql = substr($s, 0, -2);

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
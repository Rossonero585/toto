<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 14/10/17
 * Time: 22:14
 */

namespace Repositories;

use Builders\Providers\TotoFromArray;
use Builders\TotoBuilder;
use Models\Toto;

class TotoRepository extends Repository
{
    const TABLE_NAME = 'toto';

    /**
     * @return Toto
     */
    public function getToto()
    {
        $sql = "SELECT * FROM `".self::TABLE_NAME."` WHERE id = :id";

        $st = $this->getCachedStatement($sql);

        $st->execute(['id' => $this->getTotoId()]);

        $row = $st->fetch();

        return TotoBuilder::createToto(new TotoFromArray($row));
    }

    /**
     * @param Toto $toto
     * @return Toto
     */
    public function addToto(Toto $toto)
    {
        $tableName = self::TABLE_NAME;

        $insertQuery = <<<EOD
REPLACE INTO `{$tableName}` (id, pot, jackpot, start_date, winner_counts, event_count)
VALUES (:id, :pot, :jackpot, :start_date, :winner_counts, :event_count)
EOD;

        $st = $this->getCachedStatement($insertQuery);

        $st->execute([
            "id" => $this->getTotoId(),
            "pot" => $toto->getPot(),
            "jackpot" => $toto->getJackPot(),
            "start_date" => $toto->getStartTime()->format("Y-m-d H:i:s"),
            "winner_counts" => serialize($toto->getWinnerCounts()),
            "event_count" => $toto->getEventCount(),
            'bookmaker' => $toto->getBookMaker()
        ]);

        return $toto;
    }

    public function updateDeviation(float $deviation)
    {
        $this->updateFieldsById(self::TABLE_NAME, $this->getTotoId(), [
            'pool_deviation' => $deviation
        ]);
    }

}
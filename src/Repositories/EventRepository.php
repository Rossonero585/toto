<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 07/10/17
 * Time: 13:34
 */

namespace Repositories;

use Builders\EventBuilder;
use Builders\Providers\EventFromArray;
use Models\Event;

class EventRepository extends Repository
{
    const TABLE_NAME = 'events';

    public function getAll()
    {
        $sql = "SELECT * FROM `".self::TABLE_NAME."` WHERE toto_id = :toto_id";

        $st = $this->getCachedStatement($sql);

        $st->execute(['toto_id' => $this->getTotoId()]);

        $events = [];

        while($row = $st->fetch(\Pdo::FETCH_ASSOC)) {

            $event = EventBuilder::createEvent(new EventFromArray($row));

            $events[] = $event;
        }

        return $events;
    }

    public function getEventById(int $id)
    {
        $tableName = self::TABLE_NAME;

        $sql = "SELECT * FROM `{$tableName}` WHERE id = :id AND toto_id = :toto_id";

        $st = $this->getCachedStatement($sql);

        $st->execute(["id" => $id, "toto_id" => $this->getTotoId()]);

        $row = $st->fetch(\PDO::FETCH_ASSOC);

        return EventBuilder::createEvent(new EventFromArray($row));
    }

    public function addEvent(Event $event)
    {
        $tableName = self::TABLE_NAME;
        $insertQuery = <<<EOD
INSERT INTO `{$tableName}` (id, title, p1, px, p2, s1, sx, s2, league, toto_id)
VALUES (:id, :title, :p1, :px, :p2, :s1, :sx, :s2, :league, :toto_id)
ON DUPLICATE KEY UPDATE
p1 = VALUES(p1),
px = VALUES(px),
p2 = VALUES(p2),
league = VALUES(league)
EOD;

        $st = $this->getCachedStatement($insertQuery);

        $st->execute([
            "id"    => $event->getId(),
            "title" => $event->getTitle(),
            "p1" => $event->getP1(),
            "px" => $event->getPx(),
            "p2" => $event->getP2(),
            "s1" => $event->getS1(),
            "sx" => $event->getSx(),
            "s2" => $event->getS2(),
            "league" => $event->getLeague(),
            "toto_id" => $this->getTotoId()
        ]);

        return $event;
    }

    public function updateEventResultById(int $id, $result)
    {
        return $this->updateFieldsById(self::TABLE_NAME, $id, [
            'result' => $result
        ]);
    }

}
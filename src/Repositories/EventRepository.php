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
        $st = $this->pdo->prepare("SELECT * FROM `".self::TABLE_NAME."`");

        $st->execute();

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

        $st = $this->pdo->prepare("SELECT * FROM `{$tableName}` WHERE id = :id");

        $st->execute(["id" => $id]);

        $row = $st->fetch(\PDO::FETCH_ASSOC);

        return EventBuilder::createEvent(new EventFromArray($row));
    }

    public function addEvent(Event $event)
    {
        $tableName = self::TABLE_NAME;
        $insertQuery = <<<EOD
REPLACE INTO `{$tableName}` (id, title, p1, px, p2, s1, sx, s2, league, is_canceled)
VALUES (:id, :title, :p1, :px, :p2, :s1, :sx, :s2, :league, :is_canceled)
EOD;

        $st = $this->pdo->prepare($insertQuery);

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
            "is_canceled" => $event->isCanceled(),
        ]);

        return $event;
    }
}
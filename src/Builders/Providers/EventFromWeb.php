<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 08/10/17
 * Time: 18:57
 */

namespace  Builders\Providers;

class EventFromWeb implements EventInterface
{
    private $totoItem;

    private $id;

    public function __construct(\stdClass $toto, int $id)
    {
        $this->totoItem = $toto;
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getP1(): float
    {
        return $this->totoItem->pwin_first / 100;
    }

    public function getPx(): float
    {
        return $this->totoItem->pdraw / 100;
    }

    public function getP2(): float
    {
        return $this->totoItem->pwin_second / 100;
    }

    public function getS1(): float
    {
        // todo
        return $this->totoItem->p_first / 100;
    }

    public function getSx(): float
    {
        // todo
        return $this->totoItem->p_draw / 100;
    }

    public function getS2(): float
    {
        // todo
        return $this->totoItem->p_second / 100;
    }

    public function getLeague(): string
    {
        return $this->totoItem->name_ch;
    }

    public function getCountry()
    {
        $matches = [];

        preg_match('/(Ч|ч)емпионат\s([\p{Cyrillic}-]+)/u', $this->getLeague(), $matches);

        return isset($matches[2]) ? $matches[2] : null;
    }

    public function getTeam1(): string
    {
        return $this->getTeamName(1);
    }

    public function getTeam2(): string
    {
        return $this->getTeamName(2);
    }

    private function getTeamName($number)
    {
        $matchName = $this->totoItem->name_ev;

        $matches = [];

        preg_match('/([\p{Cyrillic}-\s\d]+)\s-\s([\p{Cyrillic}-\s\d]+)/u', $matchName, $matches);

        return $matches[$number];
    }

    public function getTile(): string
    {
        return $this->totoItem->name_ev;
    }


    public function getResult(): ?string
    {
        $win = $this->totoItem->win;

        if (!$win) return null;

        switch ($win) {
            case 1:
                return '1';
                break;
            case 2:
                return 'X';
                break;
            case 3:
                return '2';
                break;
            case 4:
                return '4';
                break;
        }
    }


}
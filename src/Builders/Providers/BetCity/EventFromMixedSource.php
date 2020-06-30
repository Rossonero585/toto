<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 03/03/20
 * Time: 21:46
 */

namespace Builders\Providers\BetCity;

use Builders\Providers\EventInterface;

class EventFromMixedSource implements EventInterface
{
    /**
     * @var \stdClass
     */
    private $toto;

    /**
     * @var array
     */
    private $assoc;

    /**
     * @var int
     */
    private $number;

    public function __construct(\stdClass $toto, array $assoc, int $number)
    {
        $this->toto  = $toto;
        $this->assoc = $assoc;
        $this->number = $number;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getP1(): float
    {
        return $this->assoc['p1'];
    }

    public function getPx(): float
    {
        return $this->assoc['px'];
    }

    public function getP2(): float
    {
        return $this->assoc['p2'];
    }

    public function getS1(): float
    {
        return $this->toto->pwin_first / 100;
    }

    public function getSx(): float
    {
        return $this->toto->pdraw / 100;
    }

    public function getS2(): float
    {
        return $this->toto->pwin_second / 100;
    }

    public function getLeague(): string
    {
        return $this->toto->name_ch;
    }

    public function getTile(): string
    {
        return $this->assoc['title'];
    }

    public function getResult(): ?string
    {
        $win = $this->toto->win;

        if (!$win) return null;

        return (string)$win;
    }

    public function getSource(): ?string
    {
        return isset($this->assoc['source']) ? $this->assoc['source'] : '';
    }

    public function getId(): ?int
    {
        return null;
    }


}
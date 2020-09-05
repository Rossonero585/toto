<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 03/03/20
 * Time: 21:46
 */

namespace Builders\Providers;

class EventFromMixedSource implements EventInterface
{
    /** @var EventInterface */
    private $eventFromWeb;

    /** @var EventFromArray */
    private $eventFromArray;

    /**
     * @var int
     */
    private $number;

    public function __construct(EventInterface $eventFromWeb, EventFromArray $eventFromArray, int $number)
    {
        $this->eventFromWeb   = $eventFromWeb;
        $this->eventFromArray = $eventFromArray;
        $this->number         = $number;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getP1(): float
    {
        return $this->eventFromArray->getP1();
    }

    public function getPx(): float
    {
        return $this->eventFromArray->getPx();
    }

    public function getP2(): float
    {
        return $this->eventFromArray->getP2();
    }

    public function getS1(): float
    {
        return $this->eventFromWeb->getS1();
    }

    public function getSx(): float
    {
        return $this->eventFromWeb->getSx();
    }

    public function getS2(): float
    {
        return $this->eventFromWeb->getS2();
    }

    public function getLeague(): string
    {
        return $this->eventFromWeb->getLeague();
    }

    public function getTile(): string
    {
        return $this->eventFromWeb->getTile();
    }

    public function getResult(): ?string
    {
        $win = $this->eventFromWeb->getResult();

        if (!$win) return null;

        return (string)$win;
    }

    public function getSource(): ?string
    {
        return $this->eventFromArray->getSource();
    }

    public function getId(): ?int
    {
        return null;
    }


}
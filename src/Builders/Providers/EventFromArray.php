<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 08/10/17
 * Time: 18:48
 */

namespace Builders\Providers;

class EventFromArray implements EventInterface
{
    /** @var array */
    private $assoc;

    public function __construct(array $assoc)
    {
        $this->assoc = $assoc;
    }

    public function getId(): int
    {
        return $this->assoc['id'];
    }

    public function getP1(): float
    {
        return $this->assoc['s1'];
    }

    public function getPx(): float
    {
        return $this->assoc['sx'];
    }

    public function getP2(): float
    {
        return $this->assoc['s2'];
    }

    public function getS1(): float
    {
        return $this->assoc['p1'];
    }

    public function getSx(): float
    {
        return $this->assoc['px'];
    }

    public function getS2(): float
    {
        return $this->assoc['p2'];
    }

    public function getLeague(): string
    {
        return $this->assoc['league'];
    }

    public function getTile(): string
    {
        return $this->assoc['title'];
    }

    public function getResult(): ?string
    {
        return (string)$this->assoc['result'];
    }

    public function getSource(): ?string
    {
        return isset($this->assoc['source']) ? $this->assoc['source'] : '';
    }

    public function getNumber(): int
    {
        return $this->assoc['number'];
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 07/10/17
 * Time: 13:04
 */
namespace  Models;

class Bet
{
    /** @var int */
    private $id;

    /** @var  array */
    private $results;

    /** @var  float */
    private $money;

    /** @var  float */
    private $ev;

    public function __construct(int $id, float $money, float $ev, array $results)
    {
        $this->id = $id;
        $this->results = $results;
        $this->money = $money;
        $this->ev = $ev;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @param array $results
     */
    public function setResults(array $results)
    {
        $this->results = $results;
    }

    /**
     * @return float
     */
    public function getMoney(): float
    {
        return $this->money;
    }

    /**
     * @param float $money
     */
    public function setMoney(float $money)
    {
        $this->money = $money;
    }

    /**
     * @return float
     */
    public function getEv(): float
    {
        return $this->ev;
    }

    /**
     * @param float $ev
     */
    public function setEv(float $ev)
    {
        $this->ev = $ev;
    }
}
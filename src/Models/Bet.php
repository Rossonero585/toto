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

    public function __construct(int $id, float $money, array $results)
    {
        $this->id = $id;
        $this->results = $results;
        $this->money = $money;
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
}
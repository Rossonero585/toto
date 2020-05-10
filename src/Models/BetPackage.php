<?php

namespace Models;

class BetPackage
{
    /** @var int */
    private $id;

    /** @var float */
    private $money;

    /** @var float */
    private $probability;

    /** @var float */
    private $ev;

    /** @var float */
    private $income;

    /**
     * BetPackage constructor.
     * @param int $id
     * @param float $money
     * @param float $probability
     * @param float $ev
     * @param float $income
     */
    public function __construct(?int $id, ?float $money, ?float $probability, ?float $ev, ?float $income)
    {
        $this->id = $id;
        $this->money = $money;
        $this->probability = $probability;
        $this->ev = $ev;
        $this->income = $income;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
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
    public function setMoney(float $money): void
    {
        $this->money = $money;
    }

    /**
     * @return float
     */
    public function getProbability(): float
    {
        return $this->probability;
    }

    /**
     * @param float $probability
     */
    public function setProbability(float $probability): void
    {
        $this->probability = $probability;
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
    public function setEv(float $ev): void
    {
        $this->ev = $ev;
    }

    /**
     * @return float
     */
    public function getIncome(): float
    {
        return $this->income;
    }

    /**
     * @param float $income
     */
    public function setIncome(float $income): void
    {
        $this->income = $income;
    }



}

<?php

namespace Models;

use \DateTime;

class PoolItem
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var float
     */
    private $money;

    /**
     * @var string
     */
    private $result;

    /** @var DateTime */
    private $betDate;

    /**
     * PoolItem constructor.
     *
     * @param string $code
     * @param float $money
     * @param string $result
     * @param DateTime $betDate
     */
    public function __construct(string $code, float $money, string $result, DateTime $betDate = null)
    {
        $this->code = $code;
        $this->money = $money;
        $this->result = $result;
        $this->betDate = $betDate;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
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
     * @return string
     */
    public function getResult(): string
    {
        return $this->result;
    }

    /**
     * @param string $result
     */
    public function setResult(string $result): void
    {
        $this->result = $result;
    }

    /**
     * @return DateTime
     */
    public function getBetDate(): ?DateTime
    {
        return $this->betDate;
    }
}

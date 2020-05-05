<?php


namespace Models\Input;

class Bet
{
    /** @var float */
    private $money;

    /** @var array */
    private $results;

    public function __construct($money, $results)
    {
        $this->money = $money;
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
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 01/03/20
 * Time: 20:35
 */

namespace Builders\Providers;

use Models\Input\Bet;

class BetRequestFromGenerator implements BetRequestInterface
{
    /**
     * @var string
     */
    private $totoId;

    /**
     * @var string
     */
    private $bookMaker;

    /**
     * @var array
     */
    private $bets;

    /**
     * @var bool
     */
    private $isTest;

    /**
     * BetRequestFromTotoDecision constructor.
     * @param string $totoId
     * @param string $bookMaker
     * @param array $bets
     * @param bool $isTest
     */
    public function __construct(string $totoId, string $bookMaker, array $bets, bool $isTest)
    {
        $this->totoId     = $totoId;
        $this->bookMaker  = $bookMaker;
        $this->bets       = $bets;
        $this->isTest     = $isTest;
    }

    public function getTotoId() : string
    {
        return $this->totoId;
    }

    public function getBets() : array
    {
        return $this->getBetsArray();
    }

    public function getEvents() : array
    {
        return [];
    }

    public function isTest() : bool
    {
        return (bool)$this->isTest;
    }

    private function getBetsArray()
    {
        return array_map(function (array $arr) {
            return new Bet(
                50,
                $arr
            );
        }, $this->bets);
    }

}
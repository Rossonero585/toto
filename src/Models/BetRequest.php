<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 01/03/20
 * Time: 18:30
 */

namespace Models;

class BetRequest
{
    /**
     * @var int
     */
    private $totoId;

    /**
     * @var string
     */
    private $betsFile;

    /**
     * @var string
     */
    private $eventsFile;

    /**
     * @var bool
     */
    private $isTest;

    /**
     * BetRequest constructor.
     * @param int $totoId
     * @param string $betsFile
     * @param string $eventsFile
     * @param bool $isTest
     */
    public function __construct(int $totoId, string $betsFile, string $eventsFile, bool $isTest)
    {
        $this->totoId = $totoId;
        $this->betsFile = $betsFile;
        $this->eventsFile = $eventsFile;
        $this->isTest = $isTest;
    }

    /**
     * @return int
     */
    public function getTotoId(): int
    {
        return $this->totoId;
    }

    /**
     * @return string
     */
    public function getBetsFile(): string
    {
        return $this->betsFile;
    }

    /**
     * @return string
     */
    public function getEventsFile(): string
    {
        return $this->eventsFile;
    }

    /**
     * @return bool
     */
    public function isTest(): bool
    {
        return $this->isTest;
    }


}
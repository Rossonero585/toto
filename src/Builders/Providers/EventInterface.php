<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 08/10/17
 * Time: 18:35
 */

namespace Builders\Providers;

interface EventInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return float
     */
    public function getP1(): float;

    /**
     * @return float
     */
    public function getPx(): float;
    /**
     * @return float
     */
    public function getP2(): float;

    /**
     * @return float
     */
    public function getS1(): float;

    /**
     * @return float
     */
    public function getSx(): float;

    /**
     * @return float
     */
    public function getS2(): float;

    /**
     * @return string
     */
    public function getLeague(): string;

    /**
     * @return string
     */
    public function getTile(): string;

    /**
     * @return string
     */
    public function getResult(): ?string;

}
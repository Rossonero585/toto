<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 07/10/17
 * Time: 12:27
 */
namespace Models;

class Event
{
    /** @var  int */
    private $id;

    /** @var float */
    private $p1;

    /** @var float */
    private $px;

    /** @var float */
    private $p2;

    /** @var float */
    private $s1;

    /** @var float */
    private $sx;

    /** @var float */
    private $s2;

    /** @var  string */
    private $league;

    /** @var  string */
    private $title;

    /**
     * Event constructor.
     * @param $id
     * @param $p1
     * @param $px
     * @param $p2
     * @param $s1
     * @param $sx
     * @param $s2
     * @param $league
     * @param $title
     */
    public function __construct($id, $p1, $px, $p2, $s1, $sx, $s2, $league, $title)
    {
        $this->id = $id;
        $this->p1 = $p1;
        $this->px = $px;
        $this->p2 = $p2;
        $this->s1 = $s1;
        $this->sx = $sx;
        $this->s2 = $s2;
        $this->league = $league;
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getP1(): float
    {
        return $this->p1;
    }

    /**
     * @return float
     */
    public function getPx(): float
    {
        return $this->px;
    }

    /**
     * @return float
     */
    public function getP2(): float
    {
        return $this->p2;
    }

    /**
     * @return float
     */
    public function getS1(): float
    {
        return $this->s1;
    }

    /**
     * @return float
     */
    public function getSx(): float
    {
        return $this->sx;
    }

    /**
     * @return float
     */
    public function getS2(): float
    {
        return $this->s2;
    }

    /**
     * @return string
     */
    public function getLeague(): string
    {
        return $this->league;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

}
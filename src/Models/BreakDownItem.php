<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 21/10/17
 * Time: 17:31
 */

namespace Models;

class BreakDownItem
{
    private $count;

    private $pot;

    /**
     * BreakDownItem constructor.
     * @param $count
     * @param $pot
     */
    public function __construct($count, $pot)
    {
        $this->count = $count;
        $this->pot = $pot;
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }


    /**
     * @return mixed
     */
    public function getPot()
    {
        return $this->pot;
    }

    /**
     * @param mixed $pot
     */
    public function setPot($pot)
    {
        $this->pot = $pot;
    }

    /**
     * @param $pot
     * @return $this
     */
    public function addPot($pot)
    {
        $this->pot += $pot;

        return $this;
    }


}
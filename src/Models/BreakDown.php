<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 21/10/17
 * Time: 17:19
 */
namespace  Models;

class BreakDown
{
    /**
     * @var BreakDownItem[]
     */
    private $items = [];

    /**
     * BreakDown constructor.
     * @param BreakDownItem[] $items
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->items[$item->getCount()] = $item;
        }
    }

    public function getBreakDownItem(int $count)
    {
        return isset($this->items[$count]) ? $this->items[$count] : null;
    }

    public function addBreakDownItem(BreakDownItem $item)
    {
        if ($existItem = $this->getBreakDownItem($item->getCount())) {
            $existItem->addPot($item->getPot());
        }
        else {
            $this->items[$item->getCount()] = $item;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getCountItems()
    {
        return count($this->items);
    }

}
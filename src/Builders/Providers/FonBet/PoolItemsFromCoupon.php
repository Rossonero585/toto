<?php

namespace Builders\Providers\FonBet;

use Builders\Providers\PoolItemsInterface;
use Helpers\ArrayHelper;
use Models\PoolItem;

class PoolItemsFromCoupon implements PoolItemsInterface
{
    /** @var \stdClass */
    private $poolItem;

    public function __construct(\stdClass $poolItem)
    {
        $this->poolItem = $poolItem;
    }

    public function getPoolItems(): array
    {
        $betString = $this->poolItem->Options;

        $parts = explode("; ", $betString);

        $out = [];

        foreach ($parts as $part) {
            $matches = [];
            preg_match('/\(([\d|X]+)\)/', $part, $matches);

            $result = str_split($matches[1]);
            $out[] = $result;
        }

        $results = ArrayHelper::array_combination($out);

        $code = $this->poolItem->CouponCode;

        $money = (float)$this->poolItem->TotalStakeValue / count($results);

        $poolItems = [];

        foreach ($results as $key => $result) {
            array_push($poolItems, new PoolItem(
                $code."_".$key,
                $money,
                implode("", $result)
            ));
        }

        return $poolItems;
    }


}
<?php

namespace Builders\Providers\BetCity;

use Builders\Providers\PoolItemsInterface;
use Helpers\ArrayHelper;
use Models\PoolItem;

class PoolItemsFromCoupon implements PoolItemsInterface
{
    /**
     * @var string
     */
    private $line;

    public function __construct(string $line)
    {
        $this->line = $line;
    }

    public function getPoolItems(): array
    {
        $out = [];

        $items = explode("|", $this->line);

        if (count($items) > 1) {
            $code = $items[0];
            $money = (float)trim(substr($items[2], 0, -3));

            $results = explode(";", $items[3]);

            array_pop($results);

            if (preg_match("/,/", $items[3])) {

                $results = array_map(function($r) {return explode(",", $r);}, $results);
                $results = ArrayHelper::array_combination($results);

                $money = $money / count($results);

                foreach ($results as $key => $subResult) {
                    $tempCode = $code.$key;
                    array_push($out, new PoolItem(
                        $tempCode,
                        $money,
                        implode("", $subResult)
                    ));
                }
            }
            else {
                array_push($out, new PoolItem(
                    $code,
                    $money,
                    implode("", $results)
                ));
            }
        }

        return $out;
    }


}

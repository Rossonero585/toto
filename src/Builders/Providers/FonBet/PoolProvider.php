<?php

namespace Builders\Providers\FonBet;

use Builders\Providers\PoolProviderInterface;
use GuzzleHttp\Client;
use Helpers\Http\FonBetClient;
use \Generator;

use function json_decode;

class PoolProvider implements PoolProviderInterface
{
    const STEP = 200;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var int
     */
    private $drawingId;

    /**
     * @var int
     */
    private $iteration = 0;

    /**
     * @var int
     */
    private $count;

    public function __construct(int $drawingId)
    {
        $this->drawingId = $drawingId;

        $this->client = new Client(["base_uri" => $_ENV['FON_BET_URL'], "headers" => FonBetClient::mainHeaders]);
    }

    public function getPoolItem() : Generator
    {
        do {
            $response = $this->client->post('/superexpress-info/DataService.svc/GetMaxPrizeCoupons', [
                'json' => [
                    'options' => [
                        'Count' => self::STEP,
                        'DrawingId' => $this->drawingId,
                        'SortDir' => "ASC",
                        'SortField' => "CouponCode",
                        'StartFrom' => $this->iteration * self::STEP
                    ]
                ]
            ]);

            $response = json_decode($response->getBody()->getContents());

            if (!$this->count) $this->count = $response->d->Summary->TotalCount;

            $this->iteration++;

            foreach ($response->d->Items as $item) {

                $poolItemsProvider = new PoolItemsFromCoupon($item);

                foreach ($poolItemsProvider->getPoolItems() as $poolItem) {
                    yield $poolItem;
                }

            }
        }
        while ($this->count > $this->iteration * self::STEP);

    }
}
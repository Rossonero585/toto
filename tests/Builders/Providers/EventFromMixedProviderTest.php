<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 03/03/20
 * Time: 22:36
 */

namespace Tests\Builders\Providers\BetCity;

use Builders\EventBuilder;
use Builders\Providers\EventFromMixedSource;
use Builders\Providers\BetCity\EventFromWeb;
use Builders\Providers\EventFromArray;
use PHPUnit\Framework\TestCase;

class EventFromMixedProviderTest extends TestCase
{
    private function createMockEventFromWeb()
    {
        $provider = new EventFromMixedSource(
            new EventFromWeb(json_decode(file_get_contents(__DIR__ . "./../../samples/betcity/totoItem.json")), 1),
            new EventFromArray([
                'p1' => 0.65,
                'px' => 0.05,
                'p2' => 0.3,
                'title' => 'Milan - Inter',
                'number' => 1,
                'source' => 'pin'
            ]),
            1
        );

        return EventBuilder::createEvent($provider);
    }

    public function testCreateEvent()
    {
        $testEvent = $this->createMockEventFromWeb();

        $this->assertEquals(0.65, $testEvent->getP1());
        $this->assertEquals(0.05, $testEvent->getPx());
        $this->assertEquals(0.3,  $testEvent->getP2());

        $this->assertEquals(0.2882,  $testEvent->getS1());
        $this->assertEquals(0.3751,  $testEvent->getSx());
        $this->assertEquals(0.3366,  $testEvent->getS2());

        $this->assertEquals('4',  $testEvent->getResult());
        $this->assertEquals('pin',  $testEvent->getSource());
    }
}
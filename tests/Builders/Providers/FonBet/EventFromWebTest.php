<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 13/10/17
 * Time: 21:16
 */

namespace Tests\Builders\Providers\FonBet;

use Builders\Providers\FonBet\EventFromWeb;
use PHPUnit\Framework\TestCase;

class EventFromWebTest extends TestCase
{
    private function createMockEventFromWeb()
    {
        return new EventFromWeb(json_decode(file_get_contents(__DIR__ . "./../../../samples/fonbet/event.json")));
    }

    public function testGetId()
    {
        $mockBinder = $this->createMockEventFromWeb();

        $this->assertEquals(null, $mockBinder->getId());
    }

    public function testGetP1()
    {
        $mockBinder = $this->createMockEventFromWeb();

        $this->assertEquals(0.33, $mockBinder->getP1());
    }

    public function testGetPx()
    {
        $mockBinder = $this->createMockEventFromWeb();

        $this->assertEquals(0.28, $mockBinder->getPx());
    }

    public function testGetP2()
    {
        $mockBinder = $this->createMockEventFromWeb();

        $this->assertEquals(0.39, $mockBinder->getP2());
    }

    public function testGetS1()
    {
        $mockBinder = $this->createMockEventFromWeb();

        $this->assertEquals(0.309123579427, $mockBinder->getS1());
    }

    public function testGetSx()
    {
        $mockBinder = $this->createMockEventFromWeb();

        $this->assertEquals(0.268424310113, $mockBinder->getSx());
    }

    public function testGetS2()
    {
        $mockBinder = $this->createMockEventFromWeb();

        $this->assertEquals(0.422452110460, $mockBinder->getS2());
    }

    public function testGetTitle()
    {
        $mockBinder = $this->createMockEventFromWeb();

        $this->assertEquals("ЦСКА - Спартак М", $mockBinder->getTile());
    }
}

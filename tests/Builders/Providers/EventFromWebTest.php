<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 13/10/17
 * Time: 21:16
 */

namespace Tests\Builders\Providers;

use Builders\Providers\EventFromWeb;
use PHPUnit\Framework\TestCase;

class EventFromWebTest extends TestCase
{
    private function createMockEventFromWeb()
    {
        return new EventFromWeb(json_decode(file_get_contents(__DIR__."./totoItem.json")), 0);
    }

    public function testGetId()
    {
        $mockBinder = $this->createMockEventFromWeb();

        $this->assertEquals(0, $mockBinder->getId());
    }

    public function testGetP1()
    {
        $mockBinder = $this->createMockEventFromWeb();

        $this->assertEquals(0.2882, $mockBinder->getP1());
    }


    public function testGetTeam1()
    {
        $mockBinder = $this->createMockEventFromWeb();

        $this->assertEquals("Эстудиантес", $mockBinder->getTeam1());
    }

    public function testGetTeam2()
    {
        $mockBinder = $this->createMockEventFromWeb();

        $this->assertEquals("Банфилд", $mockBinder->getTeam2());
    }

    public function testGetCountry()
    {
        $mockBinder = $this->createMockEventFromWeb();

        $this->assertEquals("Аргентины", $mockBinder->getCountry());
    }

}

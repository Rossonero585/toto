<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 14/10/17
 * Time: 22:35
 */

namespace Tests\Builders\Providers;

use Builders\Providers\TotoFromWeb;
use PHPUnit\Framework\TestCase;

class TotoFromWebTest extends TestCase
{
    private function createMockTotoProvider()
    {
        return new TotoFromWeb(json_decode(file_get_contents(realpath(__DIR__."./../../samples/toto.json"))));
    }

    public function testGetPot()
    {
        $provider = $this->createMockTotoProvider();

        $this->assertEquals(243631.12, $provider->getPot());
    }

    public function testGetJackPot()
    {
        $provider = $this->createMockTotoProvider();

        $this->assertEquals(4208340.9, $provider->getJackPot());
    }

    public function testGetStartDate()
    {
        $provider = $this->createMockTotoProvider();

        $this->assertEquals("2017-10-14 21:00:00", $provider->getDateTime()->format("Y-m-d H:i:s"));
    }

    public function testEventCount()
    {
        $provider = $this->createMockTotoProvider();

        $this->assertEquals(14, $provider->getEventCount());
    }
}
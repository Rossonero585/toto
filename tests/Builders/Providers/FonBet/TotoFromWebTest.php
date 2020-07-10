<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 14/10/17
 * Time: 22:35
 */

namespace Tests\Builders\Providers\FonBet;

use Builders\Providers\FonBet\TotoFromWeb;
use PHPUnit\Framework\TestCase;

class TotoFromWebTest extends TestCase
{
    private function createMockTotoProvider()
    {
        $json = json_decode(file_get_contents(__DIR__ . "./../../../samples/fonbet/toto.json"));

        return new TotoFromWeb($json->d);
    }

    public function testGetPot()
    {
        $provider = $this->createMockTotoProvider();

        $this->assertEquals(3590667, $provider->getPot());
    }

    public function testGetJackPot()
    {
        $provider = $this->createMockTotoProvider();

        $this->assertEquals(13694406, $provider->getJackPot());
    }

    public function testGetStartDate()
    {
        $provider = $this->createMockTotoProvider();

        $this->assertEquals("2020-06-30 17:00:00", $provider->getDateTime()->format("Y-m-d H:i:s"));
    }

    public function testEventCount()
    {
        $provider = $this->createMockTotoProvider();

        $this->assertEquals(15, $provider->getEventCount());
    }
}
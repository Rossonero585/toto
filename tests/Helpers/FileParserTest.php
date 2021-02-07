<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 01/03/20
 * Time: 18:57
 */


namespace Tests\Helpers;

use Helpers\FileParser;
use PHPUnit\Framework\TestCase;

class FileParserTest extends TestCase
{
    public function testParseEventsBetCity()
    {
        $fileParser = new FileParser();

        $arr = $fileParser->parseFileWithEvents(file_get_contents(__DIR__."./../samples/betcity/out2.txt"));

        $this->assertArraySubset([
            6 => ['p1' => "0.45259", 'px' => "0.29134", 'p2' => "0.25607", 'title' => 'Легия - Ракув', 'source' => 'pin', 'number' => 6],
        ], $arr);
    }

    public function testParseEventsFonBet()
    {
        $fileParser = new FileParser();

        $arr = $fileParser->parseFileWithEvents(file_get_contents(__DIR__."./../samples/fonbet/out.txt"));

        $firstLine = $arr[0];

        $this->assertEquals('Минск - Рух Брест', $firstLine['title']);
        $this->assertEquals(0.314, $firstLine['p1']);
        $this->assertEquals(0.28382, $firstLine['px']);
        $this->assertEquals(0.40218, $firstLine['p2']);
        $this->assertEquals('pin', $firstLine['source']);
        $this->assertEquals(0, $firstLine['number']);

    }

    public function testParseBetsBetcity()
    {
        $fileParser = new FileParser();

        $arr = $fileParser->parseFileWithBets(file_get_contents(__DIR__."./../samples/betcity/matrix.csv"), 'betcity');

        $this->assertEquals("0.2992999851703644", $arr[1]['pr_1']);
        $this->assertEquals("X222211221X212", $arr[1]['cupon']);

    }

    public function testParseBetsFonbet()
    {
        $fileParser = new FileParser();

        $arr = $fileParser->parseFileWithBets(file_get_contents(__DIR__."./../samples/fonbet/matrix.csv"), 'fonbet');


        $this->assertEquals("0.3057299852371216", $arr[1]['pr_1']);
        $this->assertEquals("122X122XX211122", $arr[1]['cupon']);

    }
}
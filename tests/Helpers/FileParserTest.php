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
    public function testParseEvents()
    {
        $fileParser = new FileParser();

        $arr = $fileParser->parseFileWithEvents(file_get_contents(__DIR__."./../samples/out.txt"));

        $this->assertArraySubset([
            ['title' => 'Мидлсбро - Халл', 'p1' => 0.3, 'px' => 0.27825, 'p2' => 0.33486, 'source' => 'pin', 'number' => 0],
        ], $arr);
    }

    public function testParseBets()
    {
        $fileParser = new FileParser();

        $arr = $fileParser->parseFileWithBets(file_get_contents(__DIR__."./../samples/matrix.csv"));

        $this->assertEquals("0.2992999851703644", $arr[1]['pr_1']);
        $this->assertEquals("X222211221X212", $arr[1]['cupon']);

    }
}
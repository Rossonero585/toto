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
    public function testFileParser()
    {
        $fileParser = new FileParser();

        $arr = $fileParser->parseFileWithEvents(file_get_contents(__DIR__."/out.txt"));

        var_dump($arr);

        $this->assertArraySubset([
            ['title' => 'Мидлсбро - Халл', 'p1' => 0.38689, 'px' => 0.27825, 'p2' => 0.33486, 'id' => 1, 'source' => 'pin'],
        ], $arr);
    }
}
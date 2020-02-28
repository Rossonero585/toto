<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 03/03/20
 * Time: 22:36
 */

namespace Tests\Builders\Providers;

use Builders\Providers\EventFromMixedSource;
use PHPUnit\Framework\TestCase;

class EventFromMixedProviderTest extends TestCase
{
    private function createMockEventFromWeb()
    {
        return new EventFromMixedSource(
            json_decode(file_get_contents(__DIR__."/totoItem.json")),
            [
                'p1' => 0.65,
                'px' => 0.05,
                'p2' => 0.3,
                'title' => 'Milan - Inter',
                'id' => 1,
                'source' => 'pin'
            ],
            0
        );
    }
}
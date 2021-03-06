<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 22/10/17
 * Time: 12:10
 */

namespace Tests\Helpers;


use Helpers\EventsHelper;
use Models\Event;
use PHPUnit\Framework\TestCase;

class EventsHelperTest extends TestCase
{
    /** @var EventsHelper */
    private $eventsHelper;

    protected function setUp()
    {
        $events = [];

        array_push($events, new Event(
            1,
            0.2,
            0.3,
            0.5,
            0.4,
            0.2,
            0.4,
            '',
            ''
        ));

        array_push($events, new Event(
            2,
            0.02,
            0.48,
            0.5,
            0.4,
            0.2,
            0.4,
            '',
            ''
        ));

        array_push($events, new Event(
            3,
            0.02,
            0.31,
            0.67,
            0.4,
            0.2,
            0.4,
            '',
            ''
        ));

        $this->eventsHelper = new EventsHelper($events);
    }

    public function testGetProbabilityForResult()
    {
        $p = $this->eventsHelper->getProbabilityForResult(1, 'x');

        $this->assertEquals(0.3, $p);
    }

    public function testCalculateProbabilityOfAllEvents()
    {
        $p = $this->eventsHelper->calculateProbabilityOfAllEvents(['1', 'x', '2']);

        $this->assertEquals(0.2*0.48*0.67, $p);
    }

    public function testDeviation()
    {
        $deviation = $this->eventsHelper->getAverageDeviation();

        $this->assertEquals(
            (0.3*(0.3-0.2) + 0.5*(0.5-0.4) + 0.48*(0.48-0.2) + 0.5*(0.5-0.4) + 0.31*(0.31-0.2) + 0.67*(0.67-0.4)) / 3,
            $deviation
        );
    }
}
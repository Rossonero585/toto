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
        $p = $this->eventsHelper->getProbabilityForResult(1, 'x', false);

        $this->assertEquals(0.7, $p);
    }

    public function testCalculateProbability()
    {
        $p = $this->eventsHelper->calculateProbabilityOfCombination([1, 1, 1], [2, 3]);

        $this->assertEquals((1-0.2)*0.02*0.02, $p);

        $p = $this->eventsHelper->calculateProbabilityOfCombination(['x', 1, 2], [1, 3]);

        $this->assertEquals(0.3*(1 - 0.02)*0.67, $p);

        $p = $this->eventsHelper->calculateProbabilityOfCombination(['x', 2, 'x'], [3]);

        $this->assertEquals((1-0.3)*(1 - 0.5)*0.31, $p);
    }

    public function testCalculateProbabilityOfAllEvents()
    {
        $p = $this->eventsHelper->calculateProbabilityOfAllEvents([1, 'x', 2]);

        $this->assertEquals(0.2*0.48*0.67, $p);
    }
}
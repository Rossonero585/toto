<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 07/01/20
 * Time: 00:55
 */

namespace Tests\Helpers;

use Helpers\ScheduleHelper;
use Models\Toto;
use PHPUnit\Framework\TestCase;

class ScheduleHelperTest extends TestCase
{
    public function testGetTimeForRun()
    {
        $_ENV['TIME_BEFORE_RUN'] = 11;

        $scheduleHelper = new ScheduleHelper();

        $toto = new Toto(
            $this->getMockStartTime(10),
            300000,
            1300000,
            14,
            [],
            'test'
        );

        $this->assertEquals(2, $scheduleHelper->getTimeForRun($toto));


        $toto = new Toto(
            $this->getMockStartTime(10),
            3000000,
            1300000,
            14,
            [],
            'test'
        );

        $this->assertEquals(4, $scheduleHelper->getTimeForRun($toto));


        $toto = new Toto(
            $this->getMockStartTime(13),
            3000000,
            1300000,
            14,
            [],
            'test'
        );

        $this->assertEquals(-1, $scheduleHelper->getTimeForRun($toto));
    }

    private function getMockStartTime(int $minutes)
    {
        $dateTime = new \DateTime();

        $dateTime->setTimezone(new \DateTimeZone('UTC'));

        $dateTime->modify("+$minutes minutes");

        return $dateTime;
    }


}
<?php

namespace Tests\Helpers;

use Helpers\Arguments;
use PHPUnit\Framework\TestCase;

class ArgumentsTest extends TestCase
{
    public function testArguments()
    {
        $args = [
            'index.php',
            'command',
            '-t=101',
            '--opt=test'
        ];

        $arguments = Arguments::getArguments($args);

        $this->assertEquals('command', $arguments->getCommand());
        $this->assertEquals('101', $arguments->get('t'));
        $this->assertEquals('test', $arguments->get('opt'));
        $this->assertEquals(null, $arguments->get('opt1'));
    }
}

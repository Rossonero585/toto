<?php

namespace Helpers;

class Arguments
{
    private $rawArgs;

    private $command;

    private $arguments = [];

    private static $instance;

    private function __construct($argv)
    {
        $this->rawArgs = $argv;

        $this->command = isset($this->rawArgs[1]) ? $this->rawArgs[1] : null;

        $this->setArguments();
    }

    private function setArguments()
    {
        for ($i = 2; $i < count($this->rawArgs); $i++) {
            $arg = $this->rawArgs[$i];
            if (strpos($arg, "=") !== false) {
                list($arg, $value) = explode('=', $arg);
                $this->arguments[trim($arg, "-= \t\n\r\0\x0B")] = $value;
            }
        }
    }

    public function getCommand() : ?string
    {
        return $this->command;
    }

    public function get($alias) : ?string
    {
        return isset($this->arguments[$alias]) ? $this->arguments[$alias] : null;
    }

    public static function getArguments($argv = [])
    {
        if (!$argv) $argv = $_SERVER['argv'];

        if (!self::$instance) self::$instance = new Arguments($argv);

        return self::$instance;
    }


}

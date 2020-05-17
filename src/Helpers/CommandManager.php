<?php

namespace Helpers;

class CommandManager
{
    /**
     * @var array
     */
    private $commands;


    public function registerCommand(string $alias, callable $function)
    {
        $this->commands[$alias] = $function;
    }

    private function getCommand(string $alias)
    {
        return isset($this->commands[$alias]) ? $this->commands[$alias] : null;
    }

    public function runCommand(string $alias)
    {
        $function = $this->getCommand($alias);

        if (is_callable($function)) {
            $function();
        }
        else {
            throw new \Exception("Unknown function $alias");
        }

        $function();
    }
}
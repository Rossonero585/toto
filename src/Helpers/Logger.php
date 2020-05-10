<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 01/03/20
 * Time: 22:53
 */

namespace Helpers;

class Logger
{

    /** @var  Logger */
    private static $instance;

    /** @var  string */
    private $path;


    private function __construct()
    {
        $this->path = ROOT_DIR."/log";
    }


    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Logger();
        }

        return self::$instance;
    }

    public function log(string $path, string $subject, string $message)
    {
        $file = $this->path."/".$path.".log";

        $f = @fopen($file, "a+t");

        flock($f, LOCK_EX);

        fputs($f, PHP_EOL.$this->getStringDate()." - ".$subject.PHP_EOL.$message.PHP_EOL);

        fclose($f);
    }

    private function getStringDate()
    {
        return (new \DateTime())->format('c');
    }

}
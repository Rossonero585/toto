<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 19/10/19
 * Time: 23:38
 */

namespace Utils;


class PdoWrapper
{
    const maxCountRetries = 5;

    private $pdo;

    private $dsn;

    private $user;

    private $password;

    public function __construct($dsn, $user, $password)
    {
        $this->dsn = $dsn;
        $this->user = $user;
        $this->password = $password;
    }

    public function __call($name, $arguments)
    {
        $retries = 0;

        while (true) {

            try {
                return call_user_func_array([$this->getPdo(), $name], $arguments);
            }
            catch (\PDOException $exception) {

                if (!strpos($exception->getMessage(), 'HY000')) throw $exception;

                $retries++;

                sleep(10);

                $this->pdo = null;

                if ($retries >= self::maxCountRetries) throw $exception;
            }

        }
    }

    private function getPdo()
    {
        if (!$this->pdo) {
            $this->pdo = new \PDO($this->dsn, $this->user, $this->password);
        }

        return $this->pdo;
    }
}
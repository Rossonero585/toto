<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 14/10/17
 * Time: 22:16
 */

namespace Repositories;

use Utils;

class Repository
{

    /** @var  \PDO */
    protected $pdo;

    /** @var \PDOStatement[] */
    protected $cachedStatements = [];

    public function __construct()
    {
        $this->pdo = Utils\Pdo::getPdo();
    }

    /**
     * @param string $query
     * @return \PDOStatement
     */
    protected function getCachedStatement(string $query)
    {
        if (!isset($this->cachedStatements[$query])) {
            $this->cachedStatements[$query] = $this->pdo->prepare($query);
        }

        return $this->cachedStatements[$query];
    }

}
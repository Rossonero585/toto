<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 14/10/17
 * Time: 22:16
 */

namespace Repositories;

use Exceptions\UnknownRepository;
use Utils;

class Repository
{
    /** @var  Repository[] */
    private static $instances = [];

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

    /**
     * @param string $className
     * @return Repository
     * @throws UnknownRepository
     */
    public static function getRepository(string $className)
    {
        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = new $className();
        }

        if (!self::$instances[$className] instanceof Repository) throw new UnknownRepository($className);

        return self::$instances[$className];
    }


}
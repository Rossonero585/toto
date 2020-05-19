<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 14/10/17
 * Time: 22:16
 */

namespace Repositories;

use Exceptions\UnknownRepository;
use Helpers\Arguments;
use Utils;


class Repository
{
    /** @var  Repository[] */
    private static $instances = [];

    /** @var  \PDO */
    protected $pdo;

    /** @var \PDOStatement[] */
    protected $cachedStatements = [];

    /** @var string|null  */
    private $totoId;

    public function __construct()
    {
        $this->pdo = Utils\Pdo::getPdo();
        $this->totoId = Arguments::getArguments()->get('t');
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

            $repository = new $className();

            if (!$repository instanceof Repository) throw new UnknownRepository($className);

            self::$instances[$className] = $repository;
        }

        return self::$instances[$className];
    }


    protected function updateFieldsById(string $tableName, $id, array $fields)
    {
        $updatePart = array_reduce(array_keys($fields), function($carry, $field) {
            $carry .= "$field = :$field, ";
            return $carry;
        }, "");

        $updatePart = substr($updatePart, 0, -2);

        $sql = "UPDATE $tableName SET $updatePart WHERE id =:id";

        $st = $this->getCachedStatement($sql);

        return $st->execute($fields + ['id' => $id]);
    }

    protected function getTotoId()
    {
        return $this->totoId;
    }
}
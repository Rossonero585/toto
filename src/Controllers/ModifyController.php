<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 04/01/20
 * Time: 11:53
 */

namespace Controllers;

use Utils\Pdo;

class ModifyController
{
    public function modifySchema($pathToSqlFile)
    {
        /** @var \PDO $pdo */
        $pdo = Pdo::getPdo(true);

        $st = $pdo->query("SHOW DATABASES LIKE 'toto_%'");

        $databases = $st->fetchAll();

        $sql = file_get_contents($pathToSqlFile);

        foreach ($databases as $db) {
            $dbName = array_shift($db);

            $pdo->exec("USE $dbName");

            $pdo->exec($sql);

            echo "Updated $dbName".PHP_EOL;
        }
    }

    public function updateAllResult()
    {
        $cu = new UpdateController();

        /** @var \PDO $pdo */
        $pdo = Pdo::getPdo(true);

        $st = $pdo->query("SHOW DATABASES LIKE 'toto_%'");

        $databases = $st->fetchAll();

        foreach ($databases as $db) {

            $dbName = array_shift($db);

            $totoId = (int)array_pop(explode("_", $dbName));

            $cu->updateTotoResult($totoId);

            echo "Updated $dbName".PHP_EOL;
        }
    }
}
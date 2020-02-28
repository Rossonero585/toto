<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 01/03/20
 * Time: 20:35
 */

namespace Builders\Providers;

class BetRequestFromPost implements BetRequestInterface
{
    public function getTotoId() : int
    {
        return (int)$_POST['toto_id'];
    }

    public function getBetsFile() : string
    {
        return file_get_contents($_FILES['bets_file']);
    }

    public function getEventsFile() : string
    {
        return file_get_contents($_FILES['events_file']);
    }

    public function isTest() : bool
    {
        return (bool)$_POST['is_test'];
    }

}
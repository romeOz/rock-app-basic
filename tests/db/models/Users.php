<?php

namespace rockunit\db\models;


use apps\common\models\users\BaseUsers;

class Users extends BaseUsers
{
    public static $connection;

    public static function getConnection()
    {
        return self::$connection;
    }
}
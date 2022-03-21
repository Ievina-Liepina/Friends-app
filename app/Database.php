<?php
namespace App;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;

class Database
{
    private static $connection = null;

    /**
     * @throws Exception
     */
    public static function connection(): ?\Doctrine\DBAL\Connection
    {
        if (self::$connection === null) {
            $connectionParams = [
                'dbname' => 'social',
                'user' => 'ieva',
                'password' => 'RobertsMika_112',
                'host' => 'localhost',
                'driver' => 'pdo_mysql',
            ];
            self::$connection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams);
        }
        return self::$connection;
    }
}

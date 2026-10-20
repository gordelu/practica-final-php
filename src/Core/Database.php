<?php
namespace App\Core;

use PDO;

class Database
{
    private PDO $connection;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config.php';

        $dsn = 'mysql:host=' . $config['host']
             . ';dbname=' . $config['dbname']
             . ';charset=utf8mb4';

        $this->connection = new PDO(
            $dsn,
            $config['username'],
            $config['password'],
            [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }
}
<?php
namespace App\Core;

use PDO;
use PDOStatement;

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

    // Prepara y ejecuta una consulta con parámetros
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        $row = $this->query($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    // Para INSERT, UPDATE y DELETE: devuelve las filas afectadas
    public function execute(string $sql, array $params = []): int
    {
        return $this->query($sql, $params)->rowCount();
    }

    public function lastInsertId(): int
    {
        return (int) $this->connection->lastInsertId();
    }
}

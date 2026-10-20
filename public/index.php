<?php

require __DIR__ . '/../vendor/autoload.php';

// Comprobación de la conexión a la base de datos
try {
    $pdo = new PDO(
        'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME') . ';charset=utf8mb4',
        getenv('DB_USER'),
        getenv('DB_PASS'),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo 'Conexión a MySQL OK (PHP ' . PHP_VERSION . ')';
} catch (PDOException $e) {
    echo 'Error de conexión: ' . $e->getMessage();
}

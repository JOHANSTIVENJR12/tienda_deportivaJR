<?php
declare(strict_types=1);

// Las credenciales reales se configuran en local.php (excluido de Git).
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $config = ['host' => '127.0.0.1', 'port' => 3306, 'database' => 'tienda_deportiva_jr', 'user' => 'root', 'password' => ''];
        if (is_file(__DIR__ . '/local.php')) {
            $config = array_merge($config, require __DIR__ . '/local.php');
        }
        $pdo = new PDO(
            "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4",
            $config['user'], $config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]
        );
    }
    return $pdo;
}

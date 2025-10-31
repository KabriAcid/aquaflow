<?php

/**
 * Database connection helper using vlucas/phpdotenv and PDO
 * Usage: require_once __DIR__ . '/database.php'; $pdo = get_db_connection();
 */

// Set timezone to AFrica/Lagos
date_default_timezone_set('Africa/Lagos');

// Load Composer autoloader if available
$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

// Load .env using vlucas/phpdotenv if available
if (class_exists('\Dotenv\Dotenv')) {
    try {
        $root = dirname(__DIR__);
        $dotenv = Dotenv\Dotenv::createImmutable($root);
        $dotenv->safeLoad();
    } catch (Exception $e) {
        // ignore - we'll fallback to $_ENV
    }
}

/**
 * Get a PDO connection (singleton)
 * @return PDO
 * @throws PDOException
 */
function get_db_connection(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = $_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? '127.0.0.1';
    $dbname = $_ENV['DB_NAME'] ?? $_SERVER['DB_NAME'] ?? 'aquaflow';
    $user = $_ENV['DB_USER'] ?? $_SERVER['DB_USER'] ?? 'root';
    $pass = $_ENV['DB_PASS'] ?? $_SERVER['DB_PASS'] ?? '';

    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);
    return $pdo;
}

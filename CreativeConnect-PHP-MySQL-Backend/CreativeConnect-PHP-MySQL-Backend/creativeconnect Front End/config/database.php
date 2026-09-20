<?php
declare(strict_types=1);

/**
 * Return one shared PDO connection.
 *
 * WAMP defaults are used for local assessment work. Production credentials
 * should be supplied with DB_HOST, DB_PORT, DB_NAME, DB_USER and DB_PASS.
 */
function db(): PDO
{
    static $connection = null;

    if ($connection instanceof PDO) {
        return $connection;
    }

    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3306';
    $name = getenv('DB_NAME') ?: 'creativeconnect';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

    try {
        $connection = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $exception) {
        error_log('CreativeConnect database connection failed: ' . $exception->getMessage());
        http_response_code(500);
        exit('The database is currently unavailable. Check config/database.php and import database.sql.');
    }

    return $connection;
}

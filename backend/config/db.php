<?php
// Database configuration and PDO connection layer (Practical 8)

// Load connection settings from environment variables or fall back to local defaults
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_NAME') ?: 'synora';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

/**
 * Returns a configured PDO database connection instance.
 * @return PDO
 * @throws RuntimeException If database connection fails
 */
function getDbConnection() {
    global $dbHost, $dbPort, $dbName, $dbUser, $dbPass;

    static $pdoInstance = null;

    if ($pdoInstance !== null) {
        return $pdoInstance;
    }

    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 5
    ];

    try {
        $pdoInstance = new PDO($dsn, $dbUser, $dbPass, $options);
        return $pdoInstance;
    } catch (PDOException $e) {
        // Log technical details safely to server error log without leaking to client
        error_log('Database Connection Error: ' . $e->getMessage());

        // Throw safe user-facing exception without database credentials
        throw new RuntimeException('Database connection failed. Please ensure the local MySQL service is active and the synora database is initialized.');
    }
}

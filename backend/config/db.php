<?php
// Database configuration and connectivity layer supporting both PDO and MySQLi (Practical 8)

// Load connection settings from environment variables or fall back to local XAMPP/MySQL defaults
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort = (int)(getenv('DB_PORT') ?: 3306);
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
        error_log('PDO Database Connection Error: ' . $e->getMessage());
        throw new RuntimeException('Database connection failed. Please ensure the local MySQL service is active and the synora database is initialized.');
    }
}

/**
 * Returns a configured MySQLi database connection instance.
 * @return mysqli
 * @throws RuntimeException If database connection fails
 */
function getMysqliConnection() {
    global $dbHost, $dbPort, $dbName, $dbUser, $dbPass;

    static $mysqliInstance = null;

    if ($mysqliInstance !== null) {
        return $mysqliInstance;
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $mysqliInstance = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
        $mysqliInstance->set_charset('utf8mb4');
        return $mysqliInstance;
    } catch (mysqli_sql_exception $e) {
        error_log('MySQLi Database Connection Error: ' . $e->getMessage());
        throw new RuntimeException('MySQLi database connection failed. Please ensure local MySQL service is active.');
    }
}

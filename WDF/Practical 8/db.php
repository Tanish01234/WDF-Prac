<?php
// Practical 08 — Database Configuration & Dual Connectivity (PDO & MySQLi)
// CHARUSAT DEPSTAR Information Technology — ITUE203 Web Development Frameworks

$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort = (int)(getenv('DB_PORT') ?: 3306);
$dbName = getenv('DB_NAME') ?: 'synora';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

/**
 * Returns a configured PDO database connection instance.
 * @return PDO
 */
function getDbConnection() {
    global $dbHost, $dbPort, $dbName, $dbUser, $dbPass;
    static $pdoInstance = null;
    if ($pdoInstance !== null) return $pdoInstance;

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
        throw new RuntimeException('PDO Database connection failed: ' . $e->getMessage());
    }
}

/**
 * Returns a configured MySQLi database connection instance.
 * @return mysqli
 */
function getMysqliConnection() {
    global $dbHost, $dbPort, $dbName, $dbUser, $dbPass;
    static $mysqliInstance = null;
    if ($mysqliInstance !== null) return $mysqliInstance;

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $mysqliInstance = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
        $mysqliInstance->set_charset('utf8mb4');
        return $mysqliInstance;
    } catch (mysqli_sql_exception $e) {
        error_log('MySQLi Database Connection Error: ' . $e->getMessage());
        throw new RuntimeException('MySQLi Database connection failed: ' . $e->getMessage());
    }
}

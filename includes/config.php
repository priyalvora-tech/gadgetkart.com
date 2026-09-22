<?php
// GadgetKart database configuration
$DB_HOST = 'localhost';
$DB_NAME = 'gadgetkart';
$DB_USER = 'root';
$DB_PASS = '';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    die('Database connection failed. Please create/import the GadgetKart database first.');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

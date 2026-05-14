<?php
// Database connection manager for PharmaSync Middleware

function getDBConnection($dbName = 'pharmasync_core') {
    $host = '127.0.0.1';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$dbName;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        // Return null or throw error
        die(json_encode(['error' => 'Database connection failure: ' . $e->getMessage()]));
    }
}

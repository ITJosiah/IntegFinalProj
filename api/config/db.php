<?php
// Database connection manager for PharmaSync Middleware

function getDBConnection($dbName = '')
{
    // Auto-detect environment: local vs online (AwardSpace)
    $isLocal = (php_sapi_name() === 'cli') || in_array($_SERVER['HTTP_HOST'] ?? '', ['127.0.0.1', 'localhost', 'localhost:8000']);

    if ($isLocal) {
        // LOCALHOST CONFIGURATION (For Development)
        $host = '127.0.0.1';
        $user = 'root';
        $pass = ''; // Default XAMPP has no password
        $realDbName = 'pharmasync_db'; // The unified local database
    } else {
        // INFINITYFREE CONFIGURATION (Live Deployment)
        $host = 'sql306.infinityfree.com';
        $user = 'if0_41951737';
        $pass = '6cv9nOfhQw'; 
        $realDbName = 'if0_41951737_pharmasync';
    }
    
    $charset = 'utf8mb4';
    $dsn = "mysql:host=$host;dbname=$realDbName;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        // Return null or throw error
        die(json_encode(['error' => 'Database connection failure: ' . $e->getMessage()]));
    }
}

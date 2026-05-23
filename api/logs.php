<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/db.php';
header('Content-Type: application/json');

$pharma = isset($_GET['pharma']) ? $_GET['pharma'] : '';

// Authentication check
if (!isset($_SESSION['user_role']) || 
    ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'pharmacy') || 
    ($_SESSION['user_role'] === 'pharmacy' && $_SESSION['pharma_code'] !== $pharma)) {
    
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

if (!$pharma) {
    echo json_encode(['status' => 'error', 'message' => 'Pharmacy code is required']);
    exit;
}

try {
    $coreDB = getDBConnection('pharmasync_core');
    
    // Fetch logs for this specific pharmacy, newest first
    $stmt = $coreDB->prepare("
        SELECT id, action, message, created_at 
        FROM logs 
        WHERE pharmacy_code = :code 
        ORDER BY created_at DESC 
        LIMIT 200
    ");
    $stmt->execute(['code' => $pharma]);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'data' => $logs]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Content-Type: application/json");

// Session security check: Only administrators can access admin metrics
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized access. Administrator session required.']);
    exit;
}

require_once 'config/db.php';

// Try to connect to the unified database
// getDBConnection() will handle any fatal errors internally, but we can also check
$core_pdo = getDBConnection();

// Since all pharmacies now share the same database, they share the same status
$status = $core_pdo ? "ONLINE" : "OFFLINE";
$nodes = [
    'laurents' => $status,
    'jrm' => $status,
    'riteaid' => $status
];

// Fetch dynamic pharmacy directory and metadata from core database
$pharmacies_data = [];
try {
    $pharmacies_data = $core_pdo->query("SELECT id, name, code, address, contact_number, email, is_open, last_sync FROM pharmacies ORDER BY id ASC")->fetchAll();
} catch (\Exception $e) {
}

// Safely pull logs using correct column 'created_at' as 'timestamp'
$webhook_logs = [];
$audit_trails = [];
$suggestions = [];
try {
    $webhook_logs = $core_pdo->query("SELECT pharmacy_code, message as payload, created_at as timestamp FROM logs ORDER BY id DESC LIMIT 5")->fetchAll();
    $audit_trails = $core_pdo->query("SELECT created_at as timestamp, pharmacy_code, message FROM logs ORDER BY id DESC LIMIT 5")->fetchAll();
    $suggestions = $core_pdo->query("SELECT id, name, email, suggestion, category, upvotes, created_at FROM suggestions ORDER BY id DESC")->fetchAll();
} catch (\Exception $e) {
}

echo json_encode([
    "nodes" => $nodes,
    "pharmacies" => $pharmacies_data,
    "webhook_logs" => $webhook_logs,
    "audit_trails" => $audit_trails,
    "suggestions" => $suggestions
]);
?>
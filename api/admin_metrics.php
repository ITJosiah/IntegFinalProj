<?php
header("Content-Type: application/json");

$host = 'localhost';
$user = 'root';
$pass = '';

function check_db_connectivity($host, $user, $pass, $db_name)
{
    try {
        $link = new PDO("mysql:host=$host;dbname=$db_name", $user, $pass, [
            PDO::ATTR_TIMEOUT => 1,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        return "ONLINE";
    } catch (\Exception $e) {
        return "OFFLINE";
    }
}

$nodes = [
    'laurents' => check_db_connectivity($host, $user, $pass, 'pharmacy_laurents'),
    'jrm' => check_db_connectivity($host, $user, $pass, 'pharmacy_jrm'),
    'riteaid' => check_db_connectivity($host, $user, $pass, 'pharmacy_riteaid')
];

try {
    $core_pdo = new PDO("mysql:host=$host;dbname=pharmasync_core", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (\Exception $e) {
    echo json_encode([
        "nodes" => $nodes,
        "pharmacies" => [],
        "webhook_logs" => [],
        "audit_trails" => [],
        "error" => "Master Core Offline: " . $e->getMessage()
    ]);
    exit;
}

// Fetch dynamic pharmacy directory and metadata from core database
$pharmacies_data = [];
try {
    $pharmacies_data = $core_pdo->query("SELECT id, name, code, address, contact_number, email, is_open, last_sync FROM pharmacies ORDER BY id ASC")->fetchAll();
} catch (\Exception $e) {
}

// Safely pull logs using correct column 'created_at' as 'timestamp'
$webhook_logs = [];
$audit_trails = [];
try {
    $webhook_logs = $core_pdo->query("SELECT pharmacy_code, message as payload, created_at as timestamp FROM logs ORDER BY id DESC LIMIT 5")->fetchAll();
    $audit_trails = $core_pdo->query("SELECT created_at as timestamp, pharmacy_code, message FROM logs ORDER BY id DESC LIMIT 5")->fetchAll();
} catch (\Exception $e) {
}

echo json_encode([
    "nodes" => $nodes,
    "pharmacies" => $pharmacies_data,
    "webhook_logs" => $webhook_logs,
    "audit_trails" => $audit_trails
]);
?>
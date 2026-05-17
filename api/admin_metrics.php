<?php
header("Content-Type: application/json");

$host = 'localhost';
$user = 'root';
$pass = '';

function check_db_connectivity($host, $user, $pass, $db_name) {
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
    'jrmp'     => check_db_connectivity($host, $user, $pass, 'pharmacy_jrmp'),
    'jas5'     => check_db_connectivity($host, $user, $pass, 'pharmacy_jas5')
];

try {
    $core_pdo = new PDO("mysql:host=$host;dbname=pharmasync_core", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (\Exception $e) {
    echo json_encode([
        "metrics" => ["total_stock" => 0, "total_skus" => 0, "total_customers" => 0],
        "nodes" => $nodes,
        "webhook_logs" => [],
        "audit_trails" => [],
        "error" => "Master Core Offline: " . $e->getMessage()
    ]);
    exit;
}

// Safely count customers
$customers_count = 0;
try {
    $customers_count = $core_pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
} catch(\Exception $e){}

$total_skus = 0;
$total_stock = 0;

foreach (['pharmacy_laurents', 'pharmacy_jrmp', 'pharmacy_jas5'] as $db) {
    if ($nodes[str_replace('pharmacy_', '', $db)] === "ONLINE") {
        try {
            $node_pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
            $total_skus += $node_pdo->query("SELECT COUNT(*) FROM medicines")->fetchColumn();
            $total_stock += $node_pdo->query("SELECT SUM(stock) FROM medicines")->fetchColumn() ?: 0;
        } catch (\Exception $e) {}
    }
}

// Safely pull logs
$webhook_logs = [];
$audit_trails = [];
try {
    $webhook_logs = $core_pdo->query("SELECT pharmacy_code, message as payload, timestamp FROM logs ORDER BY id DESC LIMIT 5")->fetchAll();
    $audit_trails = $core_pdo->query("SELECT timestamp, pharmacy_code, message FROM logs ORDER BY id DESC LIMIT 5")->fetchAll();
} catch(\Exception $e){}

echo json_encode([
    "metrics" => [
        "total_stock" => (int)$total_stock,
        "total_skus" => (int)$total_skus,
        "total_customers" => (int)$customers_count
    ],
    "nodes" => $nodes,
    "webhook_logs" => $webhook_logs,
    "audit_trails" => $audit_trails
]);
?>
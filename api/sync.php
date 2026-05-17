<?php
require_once 'config/db.php';
header('Content-Type: application/json');

// Catching the Letter
$envelope = file_get_contents("php://input");
$report = json_decode($envelope, true);

if (!$report || !isset($report['pharmacy_code'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid sync payload']);
    exit;
}

$pharmaCode = $report['pharmacy_code'];
$medicineId = $report['medicine_id'];
$medicineName = $report['medicine_name'];
$qtySold = isset($report['qty_sold']) ? (int)$report['qty_sold'] : 1;
$currentStock = (int)$report['remaining_stock'];

// 1. Update Core last_sync
$coreDB = getDBConnection('pharmasync_core');
$stmt = $coreDB->prepare("UPDATE pharmacies SET last_sync = CURRENT_TIMESTAMP WHERE code = :code");
$stmt->execute(['code' => $pharmaCode]);

// 2. Log transaction
$names = [
    'laurents' => "Laurent's Pharmacy",
    'jrmp' => "JRMP Doctors Pharmacy",
    'jas5' => "JAS5 Pharmacy"
];
$pharmaName = $names[$pharmaCode] ?? $pharmaCode;

$message = "System received a report from $pharmaName: $medicineName dispensed $qtySold units. Current stock is now at $currentStock units.";
$stmtLog = $coreDB->prepare("INSERT INTO logs (pharmacy_code, action, message) VALUES (:code, 'POS_SALE', :msg)");
$stmtLog->execute([
    'code' => $pharmaCode,
    'msg' => $message
]);

// 3. Ensure local pharmacy DB is synced
$dbs = [
    'laurents' => 'pharmacy_laurents',
    'jrmp' => 'pharmacy_jrmp',
    'jas5' => 'pharmacy_jas5'
];
if (isset($dbs[$pharmaCode])) {
    $db = getDBConnection($dbs[$pharmaCode]);
    $stmtUpdate = $db->prepare("UPDATE products SET stock = :stock WHERE id = :id");
    $stmtUpdate->execute([
        'stock' => $currentStock,
        'id' => $medicineId
    ]);
}

// The Shout-Out response
echo json_encode([
    'status' => 'success',
    'message' => $message
]);

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
$medicineId = $report['medicine_id'] ?? null;
$medicineName = $report['medicine_name'] ?? '';

$qtySold = isset($report['qty_sold']) ? (int) $report['qty_sold'] : 1;
$currentStock = isset($report['remaining_stock']) ? (int) $report['remaining_stock'] : 0;
$isUpdate = isset($report['is_update']) && $report['is_update'] === true;

// 1. Update Core last_sync
$coreDB = getDBConnection('pharmasync_core');
$stmt = $coreDB->prepare("UPDATE pharmacies SET last_sync = CURRENT_TIMESTAMP WHERE code = :code");
$stmt->execute(['code' => $pharmaCode]);

// 2. Log transaction
$names = [
    'laurents' => "Laurent's Pharmacy",
    'jrm' => "JRM DOCTORS Pharmacy",
    'riteaid' => "D' Rite Aid Generics Pharmacy"
];
$pharmaName = $names[$pharmaCode] ?? $pharmaCode;

if (isset($report['custom_action']) && isset($report['custom_message'])) {
    $actionType = trim($report['custom_action']);
    $message = trim($report['custom_message']);
} else {
    if ($isUpdate) {
        $message = "System received an update from $pharmaName: $medicineName details updated. Current stock is at $currentStock units.";
        $actionType = 'PRODUCT_UPDATE';
    } else {
        $message = "System received a report from $pharmaName: $medicineName dispensed $qtySold units. Current stock is now at $currentStock units.";
        $actionType = 'POS_SALE';
    }
}

$stmtLog = $coreDB->prepare("INSERT INTO logs (pharmacy_code, action, message) VALUES (:code, :action_type, :msg)");
$stmtLog->execute([
    'code' => $pharmaCode,
    'action_type' => $actionType,
    'msg' => $message
]);

// 3. Ensure local pharmacy DB is synced
$dbs = [
    'laurents' => 'pharmacy_laurents',
    'jrm' => 'pharmacy_jrm',
    'riteaid' => 'pharmacy_riteaid'
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

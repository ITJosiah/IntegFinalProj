<?php
require_once 'config/db.php';
header('Content-Type: application/json');

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$results = [];

// 1. Get Pharmacy Statuses from Core DB
$coreDB = getDBConnection('pharmasync_core');
$stmt = $coreDB->query("SELECT code, name, address, contact_number, is_open FROM pharmacies");
$pharmacies = [];
while ($row = $stmt->fetch()) {
    $pharmacies[$row['code']] = $row;
}

$dbs = [
    'laurents' => 'pharmacy_laurents',
    'jrm' => 'pharmacy_jrm',
    'riteaid' => 'pharmacy_riteaid'
];

foreach ($dbs as $code => $dbName) {
    if (!isset($pharmacies[$code])) continue;
    $pharmaInfo = $pharmacies[$code];

    $db = getDBConnection($dbName);
    $prefix = $code . '_';
    if ($query === '') {
        $sql = "SELECT p.id, m.generic_name, CONCAT(p.brand_name, ' ', p.strength) as brand_name, c.name as category, p.price, p.stock 
                FROM {$prefix}products p 
                JOIN {$prefix}medicines m ON p.medicine_id = m.id 
                JOIN {$prefix}categories c ON p.category_id = c.id 
                ORDER BY m.generic_name ASC";
        $stmt = $db->query($sql);
    } else {
        $sql = "SELECT p.id, m.generic_name, CONCAT(p.brand_name, ' ', p.strength) as brand_name, c.name as category, p.price, p.stock 
                FROM {$prefix}products p 
                JOIN {$prefix}medicines m ON p.medicine_id = m.id 
                JOIN {$prefix}categories c ON p.category_id = c.id 
                WHERE m.generic_name LIKE :q1 OR p.brand_name LIKE :q2 OR c.name LIKE :q3 
                ORDER BY m.generic_name ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'q1' => "%$query%",
            'q2' => "%$query%",
            'q3' => "%$query%"
        ]);
    }

    while ($med = $stmt->fetch()) {
        $med['pharmacy_code'] = $code;
        $med['pharmacy_name'] = $pharmaInfo['name'];
        $med['pharmacy_address'] = $pharmaInfo['address'];
        $med['contact_number'] = $pharmaInfo['contact_number'];
        $med['is_open'] = $pharmaInfo['is_open'];

        if ($med['stock'] > 50) {
            $med['availability'] = 'In Stock';
            $med['badge_class'] = 'badge-success';
        } elseif ($med['stock'] > 0) {
            $med['availability'] = 'Low Stock';
            $med['badge_class'] = 'badge-warning';
        } else {
            $med['availability'] = 'Out of Stock';
            $med['badge_class'] = 'badge-danger';
        }

        $results[] = $med;
    }
}

echo json_encode(['status' => 'success', 'count' => count($results), 'data' => $results]);

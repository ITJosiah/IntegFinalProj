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
    'jrmp' => 'pharmacy_jrmp',
    'jas5' => 'pharmacy_jas5'
];

foreach ($dbs as $code => $dbName) {
    if (!isset($pharmacies[$code])) continue;
    $pharmaInfo = $pharmacies[$code];

    $db = getDBConnection($dbName);
    if ($query === '') {
        $sql = "SELECT m.id, m.generic_name, b.name as brand_name, c.name as category, m.price, m.stock 
                FROM medicines m 
                JOIN brands b ON m.brand_id = b.id 
                JOIN categories c ON b.category_id = c.id 
                ORDER BY m.generic_name ASC";
        $stmt = $db->query($sql);
    } else {
        $sql = "SELECT m.id, m.generic_name, b.name as brand_name, c.name as category, m.price, m.stock 
                FROM medicines m 
                JOIN brands b ON m.brand_id = b.id 
                JOIN categories c ON b.category_id = c.id 
                WHERE m.generic_name LIKE :q OR b.name LIKE :q OR c.name LIKE :q 
                ORDER BY m.generic_name ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['q' => "%$query%"]);
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

<?php
require_once 'config/db.php';
header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : '';
$pharma = isset($_GET['pharma']) ? $_GET['pharma'] : '';

$dbs = [
    'laurents' => 'pharmacy_laurents',
    'jrmp' => 'pharmacy_jrmp',
    'jas5' => 'pharmacy_jas5'
];

if (!isset($dbs[$pharma])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid pharmacy code']);
    exit;
}

$dbName = $dbs[$pharma];

if ($action === 'get') {
    $db = getDBConnection($dbName);
    
    // Fetch Medicines
    $stmt = $db->query("SELECT m.id, m.generic_name, m.brand_id, b.name as brand_name, c.name as category, m.price, m.stock 
                        FROM medicines m 
                        JOIN brands b ON m.brand_id = b.id 
                        JOIN categories c ON b.category_id = c.id 
                        ORDER BY m.generic_name ASC");
    $medicines = $stmt->fetchAll();

    // Fetch Brands
    $stmtBrands = $db->query("SELECT b.id, b.name, b.category_id, c.name as category_name, b.manufacturer 
                              FROM brands b 
                              JOIN categories c ON b.category_id = c.id 
                              ORDER BY b.name ASC");
    $brands = $stmtBrands->fetchAll();

    // Fetch Categories
    $stmtCat = $db->query("SELECT * FROM categories ORDER BY name ASC");
    $categories = $stmtCat->fetchAll();

    $coreDB = getDBConnection('pharmasync_core');
    $stmt2 = $coreDB->prepare("SELECT is_open FROM pharmacies WHERE code = :code");
    $stmt2->execute(['code' => $pharma]);
    $pharmaInfo = $stmt2->fetch();

    echo json_encode([
        'status' => 'success', 
        'data' => $medicines, 
        'brands' => $brands,
        'categories' => $categories,
        'is_open' => $pharmaInfo['is_open']
    ]);
    exit;
}

if ($action === 'toggle_status') {
    $coreDB = getDBConnection('pharmasync_core');
    $stmt = $coreDB->prepare("UPDATE pharmacies SET is_open = NOT is_open, last_sync = CURRENT_TIMESTAMP WHERE code = :code");
    $stmt->execute(['code' => $pharma]);

    echo json_encode(['status' => 'success', 'message' => 'Store status updated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $db = getDBConnection($dbName);

    // --- CATEGORY CRUD ---
    if ($action === 'add_category') {
        $stmt = $db->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->execute(['name' => $input['category_name']]);
        echo json_encode(['status' => 'success', 'category_id' => $db->lastInsertId()]);
        exit;
    }
    if ($action === 'update_category') {
        $stmt = $db->prepare("UPDATE categories SET name = :name WHERE id = :id");
        $stmt->execute(['name' => $input['category_name'], 'id' => $input['id']]);
        echo json_encode(['status' => 'success']);
        exit;
    }
    if ($action === 'delete_category') {
        $stmt = $db->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->execute(['id' => $input['id']]);
        echo json_encode(['status' => 'success']);
        exit;
    }

    // --- BRAND CRUD ---
    if ($action === 'add_brand') {
        $stmt = $db->prepare("INSERT INTO brands (name, category_id, manufacturer) VALUES (:name, :cat_id, :man)");
        $stmt->execute([
            'name' => $input['brand_name'],
            'cat_id' => $input['category_id'],
            'man' => $input['manufacturer']
        ]);
        echo json_encode(['status' => 'success', 'brand_id' => $db->lastInsertId()]);
        exit;
    }
    if ($action === 'update_brand') {
        $stmt = $db->prepare("UPDATE brands SET name = :name, category_id = :cat_id, manufacturer = :man WHERE id = :id");
        $stmt->execute([
            'name' => $input['brand_name'],
            'cat_id' => $input['category_id'],
            'man' => $input['manufacturer'],
            'id' => $input['id']
        ]);
        echo json_encode(['status' => 'success']);
        exit;
    }
    if ($action === 'delete_brand') {
        $stmt = $db->prepare("DELETE FROM brands WHERE id = :id");
        $stmt->execute(['id' => $input['id']]);
        echo json_encode(['status' => 'success']);
        exit;
    }

    // --- MEDICINE CRUD ---
    if ($action === 'add') {
        $stmt = $db->prepare("INSERT INTO medicines (generic_name, brand_id, price, stock) VALUES (:gen, :brand_id, :price, :stock)");
        $stmt->execute([
            'gen' => $input['generic_name'],
            'brand_id' => $input['brand_id'],
            'price' => $input['price'],
            'stock' => $input['stock']
        ]);
        echo json_encode(['status' => 'success']);
        exit;
    }
    if ($action === 'update') {
        $stmt = $db->prepare("UPDATE medicines SET generic_name = :gen, brand_id = :brand_id, price = :price, stock = :stock WHERE id = :id");
        $stmt->execute([
            'gen' => $input['generic_name'],
            'brand_id' => $input['brand_id'],
            'price' => $input['price'],
            'stock' => $input['stock'],
            'id' => $input['id']
        ]);
        echo json_encode(['status' => 'success']);
        exit;
    }
    if ($action === 'delete') {
        $stmt = $db->prepare("DELETE FROM medicines WHERE id = :id");
        $stmt->execute(['id' => $input['id']]);
        echo json_encode(['status' => 'success']);
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Unknown action']);

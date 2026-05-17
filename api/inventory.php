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
    
    // Fetch Products (stocked items)
    $stmt = $db->query("SELECT p.id, m.generic_name, p.medicine_id, p.brand_name, p.strength, p.category_id, c.name as category, p.price, p.stock, p.manufacturer 
                        FROM products p 
                        JOIN medicines m ON p.medicine_id = m.id 
                        JOIN categories c ON p.category_id = c.id 
                        ORDER BY m.generic_name ASC");
    $products = $stmt->fetchAll();

    // Fetch Generic Medicines (mapped to the frontend's legacy 'brands' dropdown/table)
    $stmtBrands = $db->query("SELECT id, generic_name as name FROM medicines ORDER BY generic_name ASC");
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
        'data' => $products, 
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

    // --- GENERIC MEDICINE CRUD (Mapped to legacy 'brand' endpoints) ---
    if ($action === 'add_brand') {
        $stmt = $db->prepare("INSERT INTO medicines (generic_name) VALUES (:name)");
        $stmt->execute(['name' => $input['brand_name']]);
        echo json_encode(['status' => 'success', 'brand_id' => $db->lastInsertId()]);
        exit;
    }
    if ($action === 'update_brand') {
        $stmt = $db->prepare("UPDATE medicines SET generic_name = :name WHERE id = :id");
        $stmt->execute(['name' => $input['brand_name'], 'id' => $input['id']]);
        echo json_encode(['status' => 'success']);
        exit;
    }
    if ($action === 'delete_brand') {
        $stmt = $db->prepare("DELETE FROM medicines WHERE id = :id");
        $stmt->execute(['id' => $input['id']]);
        echo json_encode(['status' => 'success']);
        exit;
    }

    // --- PRODUCT SKU CRUD (Mapped to legacy 'medicine' endpoints) ---
    if ($action === 'add') {
        $stmt = $db->prepare("INSERT INTO products (brand_name, strength, medicine_id, category_id, price, stock, manufacturer) 
                              VALUES (:brand_name, :strength, :medicine_id, :category_id, :price, :stock, :manufacturer)");
        $stmt->execute([
            'brand_name' => $input['brand_name'] ?? 'Generic',
            'strength' => $input['strength'] ?? 'Standard',
            'medicine_id' => $input['medicine_id'],
            'category_id' => $input['category_id'],
            'price' => $input['price'],
            'stock' => $input['stock'],
            'manufacturer' => $input['manufacturer'] ?? ''
        ]);
        echo json_encode(['status' => 'success']);
        exit;
    }
    if ($action === 'update') {
        $stmt = $db->prepare("UPDATE products SET brand_name = :brand_name, strength = :strength, medicine_id = :medicine_id, category_id = :category_id, price = :price, stock = :stock, manufacturer = :manufacturer WHERE id = :id");
        $stmt->execute([
            'brand_name' => $input['brand_name'],
            'strength' => $input['strength'],
            'medicine_id' => $input['medicine_id'],
            'category_id' => $input['category_id'],
            'price' => $input['price'],
            'stock' => $input['stock'],
            'manufacturer' => $input['manufacturer'],
            'id' => $input['id']
        ]);
        echo json_encode(['status' => 'success']);
        exit;
    }
    if ($action === 'delete') {
        $stmt = $db->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute(['id' => $input['id']]);
        echo json_encode(['status' => 'success']);
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Unknown action']);

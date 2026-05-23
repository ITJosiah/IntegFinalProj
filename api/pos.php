<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/db.php';
header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : '';
$pharma = isset($_GET['pharma']) ? $_GET['pharma'] : '';

// Session security: pharmacy or admin only
if (!isset($_SESSION['user_role']) || 
    ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'pharmacy') || 
    ($_SESSION['user_role'] === 'pharmacy' && $_SESSION['pharma_code'] !== $pharma)) {
    
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

$dbs = [
    'laurents' => 'pharmacy_laurents',
    'jrm' => 'pharmacy_jrm',
    'riteaid' => 'pharmacy_riteaid'
];

if (!isset($dbs[$pharma])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid pharmacy code']);
    exit;
}

$dbName = $dbs[$pharma];

// ── GET PRODUCTS (with stock info for POS grid) ──
if ($action === 'get_products') {
    $db = getDBConnection($dbName);
    $stmt = $db->query("SELECT p.id, CONCAT(p.brand_name, ' ', p.strength) as name, m.generic_name, p.price, p.stock, c.name as category
                        FROM products p 
                        JOIN medicines m ON p.medicine_id = m.id 
                        JOIN categories c ON p.category_id = c.id 
                        ORDER BY p.brand_name ASC");
    $products = $stmt->fetchAll();
    
    echo json_encode(['status' => 'success', 'data' => $products]);
    exit;
}

// ── GET SALES HISTORY ──
if ($action === 'get_history') {
    $db = getDBConnection($dbName);
    
    // Check if sales table exists
    try {
        $stmt = $db->query("SELECT s.*, GROUP_CONCAT(CONCAT(si.product_name, ' x', si.quantity) SEPARATOR ', ') as items_summary
                           FROM sales s 
                           LEFT JOIN sale_items si ON s.id = si.sale_id 
                           GROUP BY s.id 
                           ORDER BY s.created_at DESC 
                           LIMIT 50");
        $sales = $stmt->fetchAll();
        echo json_encode(['status' => 'success', 'data' => $sales]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'success', 'data' => []]);
    }
    exit;
}

// ── PROCESS SALE ──
if ($action === 'process_sale' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!$input || !isset($input['items']) || empty($input['items'])) {
        echo json_encode(['status' => 'error', 'message' => 'Cart is empty.']);
        exit;
    }
    if (!isset($input['amount_paid']) || floatval($input['amount_paid']) <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid payment amount.']);
        exit;
    }

    $items = $input['items'];
    $amountPaid = floatval($input['amount_paid']);
    
    $db = getDBConnection($dbName);
    
    // 1. Validate all items have sufficient stock
    $totalAmount = 0;
    $validatedItems = [];
    
    foreach ($items as $item) {
        $productId = intval($item['product_id']);
        $qty = intval($item['quantity']);
        
        if ($qty <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Quantity must be at least 1.']);
            exit;
        }
        
        $stmt = $db->prepare("SELECT id, CONCAT(brand_name, ' ', strength) as name, price, stock FROM products WHERE id = :id");
        $stmt->execute(['id' => $productId]);
        $product = $stmt->fetch();
        
        if (!$product) {
            echo json_encode(['status' => 'error', 'message' => "Product #$productId not found."]);
            exit;
        }
        
        if ($product['stock'] < $qty) {
            echo json_encode(['status' => 'error', 'message' => "Insufficient stock for {$product['name']}. Available: {$product['stock']}, Requested: $qty"]);
            exit;
        }
        
        $subtotal = round($product['price'] * $qty, 2);
        $totalAmount += $subtotal;
        
        $validatedItems[] = [
            'product_id' => $productId,
            'product_name' => $product['name'],
            'quantity' => $qty,
            'unit_price' => $product['price'],
            'subtotal' => $subtotal,
            'current_stock' => $product['stock']
        ];
    }
    
    // 2. Check payment is sufficient
    if ($amountPaid < $totalAmount) {
        echo json_encode(['status' => 'error', 'message' => 'Insufficient payment. Total is ₱' . number_format($totalAmount, 2)]);
        exit;
    }
    
    $changeAmount = round($amountPaid - $totalAmount, 2);
    
    // 3. Generate receipt number
    $receiptNo = 'RX-' . strtoupper($pharma) . '-' . date('ymdHis');
    
    // 4. Begin transaction
    try {
        $db->beginTransaction();
        
        // Insert sale record
        $stmt = $db->prepare("INSERT INTO sales (receipt_no, total_amount, amount_paid, change_amount) VALUES (:receipt_no, :total, :paid, :change_amt)");
        $stmt->execute([
            'receipt_no' => $receiptNo,
            'total' => $totalAmount,
            'paid' => $amountPaid,
            'change_amt' => $changeAmount
        ]);
        $saleId = $db->lastInsertId();
        
        // Insert sale items and deduct stock
        $stmtItem = $db->prepare("INSERT INTO sale_items (sale_id, product_id, product_name, quantity, unit_price, subtotal) VALUES (:sale_id, :product_id, :product_name, :quantity, :unit_price, :subtotal)");
        $stmtStock = $db->prepare("UPDATE products SET stock = stock - :qty WHERE id = :id");
        
        foreach ($validatedItems as $vi) {
            $stmtItem->execute([
                'sale_id' => $saleId,
                'product_id' => $vi['product_id'],
                'product_name' => $vi['product_name'],
                'quantity' => $vi['quantity'],
                'unit_price' => $vi['unit_price'],
                'subtotal' => $vi['subtotal']
            ]);
            
            $stmtStock->execute([
                'qty' => $vi['quantity'],
                'id' => $vi['product_id']
            ]);
        }
        
        $db->commit();
        
        // 5. Sync to admin audit log
        $names = [
            'laurents' => "Laurent's Pharmacy",
            'jrm' => "JRM DOCTORS Pharmacy",
            'riteaid' => "D' Rite Aid Generics Pharmacy"
        ];
        $pharmaName = $names[$pharma] ?? $pharma;
        
        $itemsSummary = [];
        foreach ($validatedItems as $vi) {
            $itemsSummary[] = $vi['product_name'] . ' x' . $vi['quantity'];
        }
        $itemsStr = implode(', ', $itemsSummary);
        
        $coreDB = getDBConnection('pharmasync_core');
        
        // Update last_sync
        $stmt = $coreDB->prepare("UPDATE pharmacies SET last_sync = CURRENT_TIMESTAMP WHERE code = :code");
        $stmt->execute(['code' => $pharma]);
        
        // Insert audit log
        $message = "POS Sale completed at $pharmaName — Receipt $receiptNo: $itemsStr. Total: ₱" . number_format($totalAmount, 2) . ", Paid: ₱" . number_format($amountPaid, 2) . ", Change: ₱" . number_format($changeAmount, 2);
        $stmtLog = $coreDB->prepare("INSERT INTO logs (pharmacy_code, action, message) VALUES (:code, :action, :msg)");
        $stmtLog->execute([
            'code' => $pharma,
            'action' => 'POS_SALE',
            'msg' => $message
        ]);
        
        echo json_encode([
            'status' => 'success',
            'receipt_no' => $receiptNo,
            'total' => $totalAmount,
            'paid' => $amountPaid,
            'change' => $changeAmount,
            'items' => $validatedItems
        ]);
        
    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Transaction failed: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Unknown action']);

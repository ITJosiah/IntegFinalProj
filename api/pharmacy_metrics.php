<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
require_once 'config/db.php';

date_default_timezone_set('Asia/Manila');

$pharma = isset($_GET['pharma']) ? trim($_GET['pharma']) : '';

// Session security check: Only allow access to the metrics if:
// 1. The user is an administrator
// 2. The user is a pharmacy AND their pharmacy code matches the requested pharmacy
if (!isset($_SESSION['user_role']) || 
    ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'pharmacy') || 
    ($_SESSION['user_role'] === 'pharmacy' && $_SESSION['pharma_code'] !== $pharma)) {
    
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access. Authentication is required.']);
    exit;
}

$dbs = [
    'laurents' => 'laurents_',
    'jrm' => 'jrm_',
    'riteaid' => 'riteaid_'
];

if (!isset($dbs[$pharma])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid pharmacy code']);
    exit;
}

$prefix = $dbs[$pharma];

try {
    $db = getDBConnection();

    $period = isset($_GET['period']) ? trim($_GET['period']) : 'today';
    $todayStr = date('Y-m-d');

    switch ($period) {
        case 'weekly':
            $dateCondition = "s.created_at >= DATE_SUB(:today, INTERVAL 6 DAY)";
            break;
        case 'monthly':
            $dateCondition = "s.created_at >= DATE_SUB(:today, INTERVAL 29 DAY)";
            break;
        case 'yearly':
            $dateCondition = "s.created_at >= DATE_SUB(:today, INTERVAL 364 DAY)";
            break;
        case 'yesterday':
            $dateCondition = "DATE(s.created_at) = DATE_SUB(:today, INTERVAL 1 DAY)";
            break;
        case 'today':
        default:
            $dateCondition = "DATE(s.created_at) = :today";
            break;
    }

    // 1. Revenue & Sales Count
    $stmtToday = $db->prepare("SELECT IFNULL(SUM(s.total_amount), 0) as revenue, COUNT(*) as txn_count FROM {$prefix}sales s WHERE {$dateCondition}");
    $stmtToday->execute(['today' => $todayStr]);
    $todayMetrics = $stmtToday->fetch();

    // 2. Total Registered Products (SKUs)
    $stmtTotalProducts = $db->query("SELECT COUNT(*) as total_skus FROM {$prefix}products");
    $totalSKUs = $stmtTotalProducts->fetchColumn();

    // 3. Stock Alerts (Calculated dynamically in PHP based on 7-day sales popularity)
    $stmtStockData = $db->query("SELECT p.id, p.brand_name, p.strength, m.generic_name, p.stock, 
                                        IFNULL(sales_last_7.qty_sold, 0) as units_sold
                                 FROM {$prefix}products p
                                 JOIN {$prefix}medicines m ON p.medicine_id = m.id
                                 LEFT JOIN (
                                     SELECT product_id, SUM(quantity) as qty_sold
                                     FROM {$prefix}sale_items si
                                     JOIN {$prefix}sales s ON si.sale_id = s.id
                                     WHERE s.created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                                     GROUP BY product_id
                                 ) sales_last_7 ON p.id = sales_last_7.product_id
                                 ORDER BY p.stock ASC");
    $stockData = $stmtStockData->fetchAll();

    $outOfStockCount = 0;
    $lowStockCount = 0;
    $lowStockProducts = [];

    foreach ($stockData as $prod) {
        $unitsSold = intval($prod['units_sold']);
        $stock = intval($prod['stock']);
        
        // Define popularity and reorder alert threshold
        if ($unitsSold > 30) {
            $popularity = 'fast';
            $threshold = 30;
        } elseif ($unitsSold >= 5) {
            $popularity = 'normal';
            $threshold = 10;
        } else {
            $popularity = 'slow';
            $threshold = 3;
        }

        $isOutOfStock = ($stock === 0);
        $isLowStock = ($stock > 0 && $stock <= $threshold);

        if ($isOutOfStock) {
            $outOfStockCount++;
        } elseif ($isLowStock) {
            $lowStockCount++;
        }

        if ($isOutOfStock || $isLowStock) {
            $lowStockProducts[] = [
                'brand_name' => $prod['brand_name'],
                'strength' => $prod['strength'],
                'generic_name' => $prod['generic_name'],
                'stock' => $stock,
                'units_sold' => $unitsSold,
                'popularity' => $popularity,
                'threshold' => $threshold
            ];
        }
    }
    $lowStockProducts = array_slice($lowStockProducts, 0, 5);

    // 4. Top 5 Selling Products
    $stmtTopSelling = $db->prepare("SELECT si.product_name, SUM(si.quantity) as total_qty, SUM(si.subtotal) as total_revenue 
                                  FROM {$prefix}sale_items si
                                  JOIN {$prefix}sales s ON si.sale_id = s.id
                                  WHERE {$dateCondition}
                                  GROUP BY si.product_id, si.product_name 
                                  ORDER BY total_qty DESC 
                                  LIMIT 5");
    $stmtTopSelling->execute(['today' => $todayStr]);
    $topSelling = $stmtTopSelling->fetchAll();

    // 5. Recent Sales (last 5)
    $stmtRecentSales = $db->query("SELECT s.id, s.receipt_no, s.total_amount, s.created_at, 
                                   GROUP_CONCAT(CONCAT(si.product_name, ' x', si.quantity) SEPARATOR ', ') as items_summary 
                                   FROM {$prefix}sales s 
                                   LEFT JOIN {$prefix}sale_items si ON s.id = si.sale_id 
                                   GROUP BY s.id 
                                   ORDER BY s.created_at DESC 
                                   LIMIT 5");
    $recentSales = $stmtRecentSales->fetchAll();

    // 6. Recent Logs (last 5)
    $stmtLogs = $db->prepare("SELECT created_at as timestamp, action, message 
                              FROM logs 
                              WHERE pharmacy_code = :pharma 
                              ORDER BY id DESC 
                              LIMIT 5");
    $stmtLogs->execute(['pharma' => $pharma]);
    $recentLogs = $stmtLogs->fetchAll();

    echo json_encode([
        'status' => 'success',
        'metrics' => [
            'revenue_today' => floatval($todayMetrics['revenue']),
            'txns_today' => intval($todayMetrics['txn_count']),
            'total_skus' => intval($totalSKUs),
            'out_of_stock' => intval($outOfStockCount),
            'low_stock' => intval($lowStockCount)
        ],
        'top_selling' => $topSelling,
        'recent_sales' => $recentSales,
        'recent_logs' => $recentLogs,
        'low_stock_list' => $lowStockProducts
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch metrics: ' . $e->getMessage()]);
}
?>

<?php
// seed_sales.php - Web-runnable seeder for pharmacy sales data

require_once 'api/config/db.php';

date_default_timezone_set('Asia/Manila');

header('Content-Type: text/plain');

try {
    $db = getDBConnection();
    echo "Connected to database successfully.\n\n";

    $pharmacies = [
        'laurents' => [
            'name' => "Laurent's Pharmacy",
            'products' => [
                ['id' => 1, 'name' => 'Biogesic 500mg', 'price' => 5.00],
                ['id' => 2, 'name' => 'Amoxil 500mg', 'price' => 12.50],
                ['id' => 3, 'name' => 'Virlix 10mg', 'price' => 22.00],
                ['id' => 4, 'name' => 'Advil 200mg', 'price' => 8.50],
                ['id' => 5, 'name' => 'Neozep Forte', 'price' => 6.50],
                ['id' => 6, 'name' => 'Tuseran Forte', 'price' => 8.00],
                ['id' => 7, 'name' => 'Kremil-S Advance', 'price' => 9.50],
                ['id' => 8, 'name' => 'Decolgen Forte', 'price' => 6.00],
                ['id' => 9, 'name' => 'Alaxan FR', 'price' => 9.50],
                ['id' => 10, 'name' => 'Plasil 10mg', 'price' => 14.25],
                ['id' => 11, 'name' => 'Benadryl AH 25mg', 'price' => 12.00],
            ]
        ],
        'jrm' => [
            'name' => "JRM DOCTORS Pharmacy",
            'products' => [
                ['id' => 1, 'name' => 'Tempra 500mg', 'price' => 4.50],
                ['id' => 2, 'name' => 'Medicol Advance 400mg', 'price' => 9.00],
                ['id' => 3, 'name' => 'Diatabs 2mg', 'price' => 6.75],
                ['id' => 4, 'name' => 'Poten-Cee 500mg', 'price' => 7.00],
                ['id' => 5, 'name' => 'Symdex-D Standard', 'price' => 4.50],
                ['id' => 6, 'name' => 'Buscopan Venus Standard', 'price' => 24.50],
                ['id' => 7, 'name' => 'Imodium 2mg', 'price' => 11.25],
                ['id' => 8, 'name' => 'Ceelin Syrup 120ml', 'price' => 120.00],
                ['id' => 9, 'name' => 'Calcibloc 5mg', 'price' => 28.50],
                ['id' => 10, 'name' => 'Aspilet 81mg', 'price' => 3.50],
                ['id' => 11, 'name' => 'Robitussin DM Standard', 'price' => 135.00],
            ]
        ],
        'riteaid' => [
            'name' => "D' Rite Aid Generics Pharmacy",
            'products' => [
                ['id' => 1, 'name' => 'Alnix 10mg', 'price' => 20.00],
                ['id' => 2, 'name' => 'Dolfenal 500mg', 'price' => 25.00],
                ['id' => 3, 'name' => 'Solmux 500mg', 'price' => 11.25],
                ['id' => 4, 'name' => 'Biogesic 500mg', 'price' => 5.00],
                ['id' => 5, 'name' => 'Skelan 500mg', 'price' => 18.00],
                ['id' => 6, 'name' => 'Sinecod Forte 50mg', 'price' => 32.00],
                ['id' => 7, 'name' => 'Erceflora 5ml', 'price' => 45.00],
                ['id' => 8, 'name' => 'Flanax 275mg', 'price' => 26.50],
                ['id' => 9, 'name' => 'Bactrim 400mg', 'price' => 15.00],
                ['id' => 10, 'name' => 'Gardan 500mg', 'price' => 22.50],
                ['id' => 11, 'name' => 'Ponstan 500mg', 'price' => 31.00],
            ]
        ]
    ];

    // Truncate tables to make clean seed
    echo "Clearing existing sales tables...\n";
    foreach (array_keys($pharmacies) as $code) {
        $db->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $db->exec("TRUNCATE TABLE {$code}_sale_items;");
        $db->exec("TRUNCATE TABLE {$code}_sales;");
        $db->exec("SET FOREIGN_KEY_CHECKS = 1;");
    }
    // Also clear logs related to POS
    $db->exec("DELETE FROM logs WHERE action = 'POS_SALE' OR action = 'POS_SALE_COMPLETED';");
    echo "Existing sales cleared.\n\n";

    // Generate random sales over the last 7 days
    $today = new DateTime();
    
    foreach ($pharmacies as $code => $data) {
        echo "Seeding sales for {$data['name']} ({$code})...\n";
        $totalSalesCount = 0;
        
        for ($dayOffset = 6; $dayOffset >= 0; $dayOffset--) {
            // Determine transaction count for this day
            // More sales on today (offset 0) and weekends
            $targetDate = clone $today;
            $targetDate->sub(new DateInterval("P{$dayOffset}D"));
            $dateString = $targetDate->format('Y-m-d');
            
            $numTransactions = ($dayOffset === 0) ? rand(8, 15) : rand(3, 8);
            
            for ($txn = 0; $txn < $numTransactions; $txn++) {
                // Generate transaction details
                if ($dayOffset === 0) {
                    // For today, only generate transactions in the past of current time
                    $currentHour = intval(date('H'));
                    $currentMinute = intval(date('i'));
                    
                    if ($currentHour <= 8) {
                        $hour = rand(0, $currentHour);
                    } else {
                        $hour = rand(8, $currentHour);
                    }
                    
                    if ($hour === $currentHour) {
                        $minute = rand(0, max(0, $currentMinute - 1));
                    } else {
                        $minute = rand(0, 59);
                    }
                } else {
                    // For previous days, distribute across business hours (8 AM to 8 PM)
                    $hour = rand(8, 19);
                    $minute = rand(0, 59);
                }
                $second = rand(0, 59);
                $txnTime = "{$dateString} {$hour}:{$minute}:{$second}";

                // Create a random cart of 1 to 4 unique products
                $cartItems = [];
                $numItems = rand(1, 4);
                $selectedKeys = array_rand($data['products'], $numItems);
                if (!is_array($selectedKeys)) {
                    $selectedKeys = [$selectedKeys];
                }

                $totalAmount = 0;
                $itemSummaries = [];

                foreach ($selectedKeys as $key) {
                    $prod = $data['products'][$key];
                    $qty = rand(1, 10);
                    $subtotal = $prod['price'] * $qty;
                    $totalAmount += $subtotal;
                    
                    $cartItems[] = [
                        'product_id' => $prod['id'],
                        'product_name' => $prod['name'],
                        'quantity' => $qty,
                        'unit_price' => $prod['price'],
                        'subtotal' => $subtotal
                    ];

                    $itemSummaries[] = "{$prod['name']} x{$qty}";
                }

                $amountPaid = ceil($totalAmount / 50) * 50; // Round up to nearest 50 php note
                if ($amountPaid < $totalAmount) {
                    $amountPaid = $totalAmount;
                }
                $changeAmount = $amountPaid - $totalAmount;
                $receiptNo = 'RX-' . strtoupper($code) . '-' . $targetDate->format('ymd') . sprintf("%06d", rand(100, 99999));

                // 1. Insert Sales Record
                $stmtSale = $db->prepare("INSERT INTO {$code}_sales (receipt_no, total_amount, amount_paid, change_amount, created_at) 
                                           VALUES (:receipt_no, :total, :paid, :change, :created)");
                $stmtSale->execute([
                    'receipt_no' => $receiptNo,
                    'total' => $totalAmount,
                    'paid' => $amountPaid,
                    'change' => $changeAmount,
                    'created' => $txnTime
                ]);
                $saleId = $db->lastInsertId();

                // 2. Insert Sale Items
                $stmtItem = $db->prepare("INSERT INTO {$code}_sale_items (sale_id, product_id, product_name, quantity, unit_price, subtotal) 
                                           VALUES (:sale_id, :product_id, :product_name, :qty, :price, :sub)");
                foreach ($cartItems as $item) {
                    $stmtItem->execute([
                        'sale_id' => $saleId,
                        'product_id' => $item['product_id'],
                        'product_name' => $item['product_name'],
                        'qty' => $item['quantity'],
                        'price' => $item['unit_price'],
                        'sub' => $item['subtotal']
                    ]);
                }

                // 3. Insert into core audit logs
                $itemsStr = implode(', ', $itemSummaries);
                $message = "POS Sale completed at {$data['name']} — Receipt {$receiptNo}: {$itemsStr}. Total: ₱" . number_format($totalAmount, 2) . ", Paid: ₱" . number_format($amountPaid, 2) . ", Change: ₱" . number_format($changeAmount, 2);
                
                $stmtLog = $db->prepare("INSERT INTO logs (pharmacy_code, action, message, created_at) VALUES (:code, 'POS_SALE', :msg, :created)");
                $stmtLog->execute([
                    'code' => $code,
                    'msg' => $message,
                    'created' => $txnTime
                ]);

                $totalSalesCount++;
            }
        }
        echo "Seeded {$totalSalesCount} transactions for {$code}.\n\n";
    }

    // Enforce specific test case for Popularity-Based Stock alerts in laurents
    echo "Enforcing test cases for Laurent's Pharmacy alerts...\n";
    
    // 1. Set stocks
    // Biogesic (id = 1) -> stock = 25 (Fast-moving: will trigger alert since threshold = 30)
    // Neozep Forte (id = 5) -> stock = 8 (Normal: will trigger alert since threshold = 10)
    // Decolgen Forte (id = 8) -> stock = 5 (Slow-moving: will NOT trigger alert since threshold = 3)
    $db->exec("UPDATE laurents_products SET stock = 25 WHERE id = 1;");
    $db->exec("UPDATE laurents_products SET stock = 8 WHERE id = 5;");
    $db->exec("UPDATE laurents_products SET stock = 5 WHERE id = 8;");
    
    // 2. Ensure sales counts in the last 7 days:
    // Delete existing sales for these specific products in the last 7 days to keep test state exact
    $db->exec("DELETE si FROM laurents_sale_items si JOIN laurents_sales s ON si.sale_id = s.id WHERE si.product_id IN (1, 5, 8) AND s.created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY);");
    
    // Now insert a sale for Biogesic with qty = 45 (makes it Fast-Moving)
    $stmtSale1 = $db->prepare("INSERT INTO laurents_sales (receipt_no, total_amount, amount_paid, change_amount, created_at) VALUES (:receipt, 225.00, 250.00, 25.00, DATE_SUB(NOW(), INTERVAL 1 HOUR))");
    $receipt1 = 'RX-LAURENTS-TEST001';
    $stmtSale1->execute(['receipt' => $receipt1]);
    $saleId1 = $db->lastInsertId();
    
    $stmtItem1 = $db->prepare("INSERT INTO laurents_sale_items (sale_id, product_id, product_name, quantity, unit_price, subtotal) VALUES (:sale_id, 1, 'Biogesic 500mg', 45, 5.00, 225.00)");
    $stmtItem1->execute(['sale_id' => $saleId1]);

    // Insert a sale for Neozep Forte with qty = 15 (makes it Normal-Moving)
    $stmtSale2 = $db->prepare("INSERT INTO laurents_sales (receipt_no, total_amount, amount_paid, change_amount, created_at) VALUES (:receipt, 97.50, 100.00, 2.50, DATE_SUB(NOW(), INTERVAL 2 HOUR))");
    $receipt2 = 'RX-LAURENTS-TEST002';
    $stmtSale2->execute(['receipt' => $receipt2]);
    $saleId2 = $db->lastInsertId();
    
    $stmtItem2 = $db->prepare("INSERT INTO laurents_sale_items (sale_id, product_id, product_name, quantity, unit_price, subtotal) VALUES (:sale_id, 5, 'Neozep Forte', 15, 6.50, 97.50)");
    $stmtItem2->execute(['sale_id' => $saleId2]);

    // Insert a sale for Decolgen Forte with qty = 1 (makes it Slow-Moving)
    $stmtSale3 = $db->prepare("INSERT INTO laurents_sales (receipt_no, total_amount, amount_paid, change_amount, created_at) VALUES (:receipt, 6.00, 10.00, 4.00, DATE_SUB(NOW(), INTERVAL 3 HOUR))");
    $receipt3 = 'RX-LAURENTS-TEST003';
    $stmtSale3->execute(['receipt' => $receipt3]);
    $saleId3 = $db->lastInsertId();
    
    $stmtItem3 = $db->prepare("INSERT INTO laurents_sale_items (sale_id, product_id, product_name, quantity, unit_price, subtotal) VALUES (:sale_id, 8, 'Decolgen Forte', 1, 6.00, 6.00)");
    $stmtItem3->execute(['sale_id' => $saleId3]);
    
    echo "Alert test cases set up successfully.\n\n";

    echo "Seeding completed successfully!\n";

} catch (Exception $e) {
    echo "Error seeding database: " . $e->getMessage() . "\n";
}
?>

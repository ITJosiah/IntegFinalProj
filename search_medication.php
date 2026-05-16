<?php
header('Content-Type: application/json');

// Database configuration
$host = 'localhost';
$dbname = 'pharmasync';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$query = isset($_POST['query']) ? trim($_POST['query']) : '';
$source = isset($_POST['source']) ? $_POST['source'] : 'both';

if (empty($query)) {
    echo json_encode([]);
    exit();
}

$results = [];

// Search local database
if ($source === 'local' || $source === 'both') {
    $stmt = $pdo->prepare("
        SELECT 
            m.medication_name as name,
            m.generic_name,
            m.description,
            m.dosage,
            i.quantity as stock,
            i.price,
            p.pharmacy_name as pharmacy,
            'Local DB' as source
        FROM medications m
        LEFT JOIN inventory i ON m.medication_id = i.medication_id
        LEFT JOIN pharmacies p ON i.pharmacy_id = p.pharmacy_id
        WHERE m.medication_name LIKE :query 
           OR m.generic_name LIKE :query
           OR m.description LIKE :query
        LIMIT 10
    ");
    
    $searchTerm = "%{$query}%";
    $stmt->bindParam(':query', $searchTerm);
    $stmt->execute();
    
    $localResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $results = array_merge($results, $localResults);
}

// Simulate OpenFDA API search (replace with actual API call)
if ($source === 'global' || $source === 'both') {
    // In production, you would make an actual API call to OpenFDA
    // For now, we'll add some mock data
    $mockGlobalData = [
        [
            'name' => 'Paracetamol Extended Release',
            'generic_name' => 'Acetaminophen',
            'description' => 'Pain reliever and fever reducer',
            'dosage' => '500mg',
            'stock' => 0,
            'price' => '0.00',
            'pharmacy' => 'N/A (Global Database)',
            'source' => 'OpenFDA'
        ]
    ];
    
    if (stripos($query, 'para') !== false || stripos($query, 'aceta') !== false) {
        $results = array_merge($results, $mockGlobalData);
    }
}

// If no results, return empty array
if (empty($results)) {
    echo json_encode([]);
    exit();
}

echo json_encode($results);
?>
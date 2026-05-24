<?php
// api/update_location.php
// Receives { latitude, longitude } via POST and saves to session + optionally to the DB.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['latitude']) || !isset($input['longitude'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing latitude/longitude']);
    exit;
}

$lat = floatval($input['latitude']);
$lng = floatval($input['longitude']);

// Validate coordinates
if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid coordinates']);
    exit;
}

// Store in session
$_SESSION['user_lat'] = $lat;
$_SESSION['user_lng'] = $lng;

// If logged-in customer, also persist to DB
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'customer' && isset($_SESSION['user_id'])) {
    try {
        require_once __DIR__ . '/config/db.php';
        $db = getDBConnection();
        $stmt = $db->prepare("UPDATE customers SET latitude = :lat, longitude = :lng WHERE id = :id");
        $stmt->execute([
            'lat' => $lat,
            'lng' => $lng,
            'id'  => $_SESSION['user_id'],
        ]);
    } catch (Exception $e) {
        // Non-critical — session already updated, log silently
    }
}

echo json_encode([
    'success' => true,
    'latitude' => $lat,
    'longitude' => $lng
]);

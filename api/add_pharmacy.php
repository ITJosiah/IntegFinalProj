<?php
// api/add_pharmacy.php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

// Ensure the user is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized action. Admin authentication is required.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Get the inputs
$name = trim($_POST['name'] ?? '');
$code = strtoupper(trim($_POST['code'] ?? ''));
$address = trim($_POST['address'] ?? '');
$latitude = (float)($_POST['latitude'] ?? 14.0702);
$longitude = (float)($_POST['longitude'] ?? 122.9610);
$contact_number = trim($_POST['contact_number'] ?? '');
$email = trim($_POST['email'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Basic validation
if (empty($name) || empty($code) || empty($address) || empty($username) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields (Name, Code, Address, Username, Password).']);
    exit;
}

try {
    $db = getDBConnection('pharmasync_core');

    // Check for duplicate username
    $stmt = $db->prepare("SELECT COUNT(*) FROM pharmacies WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username already exists. Please choose a different one.']);
        exit;
    }

    // Check for duplicate code
    $stmt = $db->prepare("SELECT COUNT(*) FROM pharmacies WHERE code = ?");
    $stmt->execute([$code]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Unique Code already exists. Please choose a different one.']);
        exit;
    }

    // Hash password securely
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert into pharmacies table
    // By default, a new pharmacy is marked as closed (is_open = 0) until they log in and open the store
    $stmt = $db->prepare("INSERT INTO pharmacies (name, username, password_hash, code, address, contact_number, email, latitude, longitude, is_open) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
    $stmt->execute([$name, $username, $password_hash, $code, $address, $contact_number, $email, $latitude, $longitude]);

    echo json_encode(['status' => 'success', 'message' => 'Pharmacy registered successfully!']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

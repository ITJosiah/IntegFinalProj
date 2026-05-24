<?php
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/db.php';

if (!isset($_SESSION['user_role'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$role = $_SESSION['user_role'];
$db = getDBConnection();

// Common uniqueness check function
function isUnique($db, $table, $column, $value, $excludeId = null) {
    $sql = "SELECT COUNT(*) FROM $table WHERE $column = :val";
    $params = ['val' => $value];
    if ($excludeId !== null) {
        $sql .= " AND id != :id";
        $params['id'] = $excludeId;
    }
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn() == 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($role === 'customer') {
            $id = $_SESSION['user_id'];
            $full_name = trim($_POST['full_name'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($full_name) || empty($username) || empty($email)) {
                throw new Exception('Full name, username, and email are required.');
            }

            // Check username uniqueness across all users
            if (!isUnique($db, 'customers', 'username', $username, $id) || 
                !isUnique($db, 'admins', 'username', $username) || 
                !isUnique($db, 'pharmacies', 'username', $username)) {
                throw new Exception('Username is already taken.');
            }

            if (!isUnique($db, 'customers', 'email', $email, $id)) {
                throw new Exception('Email is already registered.');
            }

            $sql = "UPDATE customers SET full_name = :fn, username = :un, email = :em";
            $params = ['fn' => $full_name, 'un' => $username, 'em' => $email, 'id' => $id];

            if (!empty($password)) {
                if (strlen($password) < 6) throw new Exception('Password must be at least 6 characters.');
                $sql .= ", password_hash = :ph";
                $params['ph'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $sql .= " WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            $_SESSION['user_name'] = $full_name; // Update session name
            echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);

        } elseif ($role === 'admin') {
            $id = $_SESSION['user_id'];
            $name = trim($_POST['name'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($name) || empty($username)) {
                throw new Exception('Name and username are required.');
            }

            // Check username uniqueness
            if (!isUnique($db, 'admins', 'username', $username, $id) || 
                !isUnique($db, 'customers', 'username', $username) || 
                !isUnique($db, 'pharmacies', 'username', $username)) {
                throw new Exception('Username is already taken.');
            }

            $sql = "UPDATE admins SET name = :n, username = :un";
            $params = ['n' => $name, 'un' => $username, 'id' => $id];

            if (!empty($password)) {
                if (strlen($password) < 6) throw new Exception('Password must be at least 6 characters.');
                $sql .= ", password_hash = :ph";
                $params['ph'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $sql .= " WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            $_SESSION['user_name'] = $name; // Update session name
            echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);

        } elseif ($role === 'pharmacy') {
            $id = $_SESSION['user_id'];
            $name = trim($_POST['name'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $contact = trim($_POST['contact_number'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($name) || empty($username) || empty($address)) {
                throw new Exception('Name, username, and address are required.');
            }

            // Check username uniqueness
            if (!isUnique($db, 'pharmacies', 'username', $username, $id) || 
                !isUnique($db, 'customers', 'username', $username) || 
                !isUnique($db, 'admins', 'username', $username)) {
                throw new Exception('Username is already taken.');
            }

            $sql = "UPDATE pharmacies SET name = :n, username = :un, address = :addr, contact_number = :cont, email = :em";
            $params = [
                'n' => $name, 'un' => $username, 'addr' => $address, 
                'cont' => $contact, 'em' => $email, 'id' => $id
            ];

            if (!empty($password)) {
                if (strlen($password) < 6) throw new Exception('Password must be at least 6 characters.');
                $sql .= ", password_hash = :ph";
                $params['ph'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $sql .= " WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            $_SESSION['user_name'] = $name; // Update session name
            echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    // GET request to fetch current details
    try {
        $id = $_SESSION['user_id'];
        if ($role === 'customer') {
            $stmt = $db->prepare("SELECT full_name, username, email FROM customers WHERE id = ?");
        } elseif ($role === 'admin') {
            $stmt = $db->prepare("SELECT name, username FROM admins WHERE id = ?");
        } elseif ($role === 'pharmacy') {
            $stmt = $db->prepare("SELECT name, username, address, contact_number, email FROM pharmacies WHERE id = ?");
        }
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        echo json_encode(['status' => 'success', 'data' => $data]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch details']);
    }
}

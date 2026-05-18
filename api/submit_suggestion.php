<?php
// api/submit_suggestion.php
require_once 'config/db.php';
header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    $db = getDBConnection('pharmasync_core');

    if ($action === 'submit') {
        // Handle guest suggestion submission
        $envelope = file_get_contents("php://input");
        $data = json_decode($envelope, true);

        if (!$data) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid or missing JSON payload.']);
            exit;
        }

        if (!isset($data['name']) || trim($data['name']) === '' || strlen(trim($data['name'])) < 2) {
            echo json_encode(['status' => 'error', 'message' => 'Your full name is required (minimum 2 characters).']);
            exit;
        }

        if (!isset($data['email']) || trim($data['email']) === '') {
            echo json_encode(['status' => 'error', 'message' => 'Email address is required.']);
            exit;
        }

        if (!filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Please provide a valid email address.']);
            exit;
        }

        if (!isset($data['category']) || trim($data['category']) === '' || strlen(trim($data['category'])) < 3) {
            echo json_encode(['status' => 'error', 'message' => 'A valid subject is required (minimum 3 characters).']);
            exit;
        }

        if (!isset($data['suggestion']) || trim($data['suggestion']) === '' || strlen(trim($data['suggestion'])) < 10) {
            echo json_encode(['status' => 'error', 'message' => 'A valid message body is required (minimum 10 characters).']);
            exit;
        }

        $name = trim($data['name']);
        $email = trim($data['email']);
        $suggestion = trim($data['suggestion']);
        $category = trim($data['category']);

        $stmt = $db->prepare("INSERT INTO suggestions (name, email, suggestion, category, upvotes) VALUES (:name, :email, :suggestion, :category, 0)");
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'suggestion' => $suggestion,
            'category' => $category
        ]);

        $newId = $db->lastInsertId();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Thank you! Your suggestion has been successfully published.',
            'data' => [
                'id' => $newId,
                'name' => $name,
                'suggestion' => $suggestion,
                'category' => $category,
                'upvotes' => 0,
                'formatted_date' => date('M d, Y')
            ]
        ]);
        exit;
    } elseif ($action === 'upvote') {
        // Handle upvote action
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid suggestion ID.']);
            exit;
        }

        $stmt = $db->prepare("UPDATE suggestions SET upvotes = upvotes + 1 WHERE id = :id");
        $stmt->execute(['id' => $id]);

        echo json_encode(['status' => 'success', 'message' => 'Upvoted successfully!']);
        exit;
    } elseif ($action === 'delete') {
        // Enforce administrative session check
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized action. Admin authentication is required.']);
            exit;
        }

        // Handle administrative suggestion deletion
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid suggestion ID.']);
            exit;
        }

        $stmt = $db->prepare("DELETE FROM suggestions WHERE id = :id");
        $stmt->execute(['id' => $id]);

        echo json_encode(['status' => 'success', 'message' => 'Inquiry deleted successfully!']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
        exit;
    }
} catch (\Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>

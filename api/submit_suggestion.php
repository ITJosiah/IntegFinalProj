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

        if (!$data || !isset($data['suggestion']) || trim($data['suggestion']) === '') {
            echo json_encode(['status' => 'error', 'message' => 'Please provide a valid suggestion text.']);
            exit;
        }

        $name = isset($data['name']) && trim($data['name']) !== '' ? trim($data['name']) : 'Anonymous Resident';
        $email = isset($data['email']) ? trim($data['email']) : '';
        $suggestion = trim($data['suggestion']);
        $category = isset($data['category']) && trim($data['category']) !== '' ? trim($data['category']) : 'General';

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
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
        exit;
    }
} catch (\Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>

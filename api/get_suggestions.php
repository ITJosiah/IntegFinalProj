<?php
// api/get_suggestions.php
require_once 'config/db.php';
header('Content-Type: application/json');

try {
    $db = getDBConnection('pharmasync_core');
    // Order by upvotes DESC, then newest created_at DESC
    $stmt = $db->query("SELECT id, name, email, suggestion, category, upvotes, DATE_FORMAT(created_at, '%b %d, %Y') as formatted_date FROM suggestions ORDER BY upvotes DESC, id DESC");
    $suggestions = $stmt->fetchAll();
    
    echo json_encode(['status' => 'success', 'data' => $suggestions]);
} catch (\Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>

<?php
require_once '../config/config.php';

header('Content-Type: application/json');

try {
    $stmt = $conn->query('SELECT * FROM users ORDER BY created_at DESC');
    $users = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $users
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
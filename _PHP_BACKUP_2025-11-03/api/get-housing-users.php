<?php
require_once '../config/config.php';

header('Content-Type: application/json');

try {
    $stmt = $conn->prepare('
        SELECT u.*, sr.role_name as subsystem_role_name 
        FROM users u 
        LEFT JOIN subsystem_roles sr ON u.subsystem_role_id = sr.id 
        WHERE u.subsystem_id = 8 
        ORDER BY u.created_at DESC
    ');
    $stmt->execute();
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
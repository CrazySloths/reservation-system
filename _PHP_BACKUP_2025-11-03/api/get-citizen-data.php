<?php
require_once '../config/config.php';

header('Content-Type: application/json');

try {
    $stmt = $conn->prepare("
        SELECT u.id, u.username, u.email, u.full_name, u.mobile_number, 
               u.gender, u.civil_status, u.nationality, u.current_address,
               u.status, u.created_at, u.last_login,
               d.name as district_name, b.name as barangay_name
        FROM users u
        LEFT JOIN districts d ON u.district_id = d.id
        LEFT JOIN barangays b ON u.barangay_id = b.id
        WHERE u.role_id = 2
        ORDER BY u.created_at DESC
    ");
    
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $users
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
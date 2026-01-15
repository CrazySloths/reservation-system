<?php
require_once '../config/config.php';

header('Content-Type: application/json');

try {
    $stmt = $conn->query("SELECT * FROM districts ORDER BY name ASC");
    $districts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $districts
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch districts: ' . $e->getMessage()
    ]);
}
?>
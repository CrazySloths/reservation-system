<?php
require_once '../config/config.php';
header('Content-Type: application/json');

try {
    $district_id = $_GET['district_id'] ?? null;
    
    if (!$district_id) {
        echo json_encode(['success' => false, 'message' => 'District ID is required']);
        exit;
    }
    
    $sql = "SELECT id, name, alternate_name 
            FROM barangays 
            WHERE district_id = ? 
            ORDER BY name ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$district_id]);
    $barangays = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $barangays]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
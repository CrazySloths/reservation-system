<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!isset($_POST['subsystem_id'])) {
    echo json_encode(['error' => 'Subsystem ID required']);
    exit;
}

$subsystem_id = $_POST['subsystem_id'];

try {
    // Check if subsystem exists
    $stmt = $conn->prepare('SELECT name FROM subsystems WHERE id = ?');
    $stmt->execute([$subsystem_id]);
    $subsystem = $stmt->fetch();
    
    if (!$subsystem) {
        echo json_encode(['error' => 'Subsystem not found']);
        exit;
    }
    
    // Get roles for this subsystem
    $stmt = $conn->prepare('SELECT id, role_name, description FROM subsystem_roles WHERE subsystem_id = ?');
    $stmt->execute([$subsystem_id]);
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($roles);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
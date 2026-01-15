<?php
require_once '../config/config.php';

header('Content-Type: application/json');

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'User ID is required'
    ]);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT valid_id_front_image, valid_id_back_image, selfie_with_id_image, valid_id_type
        FROM users 
        WHERE id = ? AND role_id = 2
    ");
    
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Citizen not found'
        ]);
        exit;
    }
    
    $documents = [];
    $upload_path = '../uploads/id_images/';
    
    if ($user['valid_id_front_image']) {
        $front_path = str_replace('../uploads/id_images/', '', $user['valid_id_front_image']);
        $documents['front'] = [
            'filename' => $front_path,
            'exists' => file_exists($upload_path . $front_path),
            'url' => $user['valid_id_front_image']
        ];
    }
    
    if ($user['valid_id_back_image']) {
        $back_path = str_replace('../uploads/id_images/', '', $user['valid_id_back_image']);
        $documents['back'] = [
            'filename' => $back_path,
            'exists' => file_exists($upload_path . $back_path),
            'url' => $user['valid_id_back_image']
        ];
    }
    
    if ($user['selfie_with_id_image']) {
        $selfie_path = str_replace('../uploads/id_images/', '', $user['selfie_with_id_image']);
        $documents['selfie'] = [
            'filename' => $selfie_path,
            'exists' => file_exists($upload_path . $selfie_path),
            'url' => $user['selfie_with_id_image']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'id_type' => $user['valid_id_type'],
            'documents' => $documents
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
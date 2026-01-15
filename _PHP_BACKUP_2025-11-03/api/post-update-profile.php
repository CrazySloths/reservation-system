<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once __DIR__ . '/../config/config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Only POST method allowed';
    echo json_encode($response);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required_fields = ['user_id', 'full_name', 'email', 'mobile_number', 'birthdate', 'gender', 'civil_status', 'nationality', 'district_id', 'barangay_id', 'current_address', 'zip_code'];
foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        $response['message'] = "Field '$field' is required";
        echo json_encode($response);
        exit;
    }
}

// Validate email format
if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Invalid email format';
    echo json_encode($response);
    exit;
}

// Validate mobile number format (Philippine format)
if (!preg_match('/^09\d{9}$/', $input['mobile_number'])) {
    $response['message'] = 'Mobile number must be in Philippine format (09xxxxxxxxx)';
    echo json_encode($response);
    exit;
}

// Check if email is already taken by another user
$stmt = $conn->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
$stmt->execute([$input['email'], $input['user_id']]);
if ($stmt->fetch()) {
    $response['message'] = 'Email address is already taken';
    echo json_encode($response);
    exit;
}

// Validate district and barangay exist
$stmt = $conn->prepare('SELECT id FROM districts WHERE id = ?');
$stmt->execute([$input['district_id']]);
if (!$stmt->fetch()) {
    $response['message'] = 'Invalid district selected';
    echo json_encode($response);
    exit;
}

$stmt = $conn->prepare('SELECT id FROM barangays WHERE id = ? AND district_id = ?');
$stmt->execute([$input['barangay_id'], $input['district_id']]);
if (!$stmt->fetch()) {
    $response['message'] = 'Invalid barangay for selected district';
    echo json_encode($response);
    exit;
}

// Handle password change if provided
if (!empty($input['new_password'])) {
    // Only verify current password if it's provided (skip for OTP-verified changes)
    if (!empty($input['current_password'])) {
        $stmt = $conn->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmt->execute([$input['user_id']]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($input['current_password'], $user['password_hash'])) {
            $response['message'] = 'Current password is incorrect';
            echo json_encode($response);
            exit;
        }
    }
    
    if (strlen($input['new_password']) < 6) {
        $response['message'] = 'New password must be at least 6 characters';
        echo json_encode($response);
        exit;
    }
}

try {
    // Prepare update query with optional password
    if (!empty($input['new_password'])) {
        $stmt = $conn->prepare('
            UPDATE users SET 
                full_name = ?, email = ?, mobile_number = ?, birthdate = ?, 
                gender = ?, civil_status = ?, nationality = ?, district_id = ?, 
                barangay_id = ?, current_address = ?, zip_code = ?, 
                password_hash = ?, updated_at = NOW() 
            WHERE id = ?
        ');
        $stmt->execute([
            $input['full_name'], $input['email'], $input['mobile_number'], $input['birthdate'],
            $input['gender'], $input['civil_status'], $input['nationality'], $input['district_id'],
            $input['barangay_id'], $input['current_address'], $input['zip_code'],
            password_hash($input['new_password'], PASSWORD_DEFAULT), $input['user_id']
        ]);
    } else {
        $stmt = $conn->prepare('
            UPDATE users SET 
                full_name = ?, email = ?, mobile_number = ?, birthdate = ?, 
                gender = ?, civil_status = ?, nationality = ?, district_id = ?, 
                barangay_id = ?, current_address = ?, zip_code = ?, 
                updated_at = NOW() 
            WHERE id = ?
        ');
        $stmt->execute([
            $input['full_name'], $input['email'], $input['mobile_number'], $input['birthdate'],
            $input['gender'], $input['civil_status'], $input['nationality'], $input['district_id'],
            $input['barangay_id'], $input['current_address'], $input['zip_code'], $input['user_id']
        ]);
    }

    $user_id = $input['user_id'];

    // Log the update
    $action = !empty($input['new_password']) ? 'profile_and_password_updated' : 'profile_updated';
    $stmt = $conn->prepare('INSERT INTO audit_logs (user_id, action, ip_address, user_agent) VALUES (?, ?, ?, ?)');
    $stmt->execute([$user_id, $action, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']);

    $response['success'] = true;
    $response['message'] = !empty($input['new_password']) ? 'Profile and password updated successfully' : 'Profile updated successfully';
    
} catch (Exception $e) {
    $response['message'] = 'Failed to update profile: ' . $e->getMessage();
}

echo json_encode($response);
?>
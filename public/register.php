<?php
// register.php
// TODO: SECURITY - Enforce HTTPS (comment out for development)
// if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
//     $redirectURL = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
//     header("Location: $redirectURL");
//     exit();
// }

// TODO: SECURITY - Secure session configuration (comment out for development)
// ini_set('session.cookie_httponly', 1);
// ini_set('session.cookie_secure', 1);
// ini_set('session.cookie_samesite', 'Strict');
// ini_set('session.use_strict_mode', 1);

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../vendor/autoload.php'; // For PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$colors = [
    'background' => '#f2f7f5',
    'headline' => '#00473e',
    'paragraph' => '#475d5b',
    'button' => '#faae2b',
    'button_text' => '#00473e',
    'highlight' => '#faae2b',
    'secondary' => '#ffa8ba',
    'tertiary' => '#fa5246',
];

$errors = [];
$success = false;

function randomToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}



function sendVerificationEmail($to, $name, $token, $otp) {
    $mail = new PHPMailer(true);
    try {
        // Optimize SMTP settings for faster sending
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'lgu1.infrastructureutilities@gmail.com';
        $mail->Password = 'kpyv rwvp tmxw zvoq';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->Timeout = 10; // Reduce timeout
        $mail->SMTPKeepAlive = true; // Keep connection alive
        
        $mail->setFrom('lgu1.infrastructureutilities@gmail.com', 'LGU1 System');
        $mail->addAddress($to, $name);
        $mail->isHTML(true);
        $mail->Subject = 'LGU1 Email Verification Code';
        
        // Simplified email template for faster processing
        $mail->Body = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9;">
            <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h2 style="color: #00473e; text-align: center; margin-bottom: 20px;">LGU1 Email Verification</h2>
                <p style="color: #333; font-size: 16px;">Hello ' . htmlspecialchars($name) . ',</p>
                <p style="color: #666; line-height: 1.6;">Please use this verification code to complete your registration:</p>
                <div style="text-align: center; margin: 30px 0;">
                    <div style="background: #faae2b; color: #00473e; font-size: 32px; font-weight: bold; padding: 15px 30px; border-radius: 8px; letter-spacing: 3px; display: inline-block;">' . $otp . '</div>
                    <p style="color: #fa5246; font-size: 12px; margin-top: 10px;">Valid for 10 minutes</p>
                </div>
                <p style="color: #666; font-size: 14px;">If you did not request this verification, please ignore this email.</p>
                <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
                <p style="color: #999; font-size: 12px; text-align: center;">Local Government Unit 1 - Registration System</p>
            </div>
        </div>';
        
        $mail->AltBody = "LGU1 Email Verification\n\nHello $name,\n\nYour verification code: $otp\n(Valid for 10 minutes)\n\nEnter this code on the registration page to complete your account setup.\n\n---\nLGU1 Registration System";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}

// Handle different registration types
$registration_type = $_POST['registration_type'] ?? 'citizen';
$step = 1;
$user_data = [];
$verification_email_sent = false;

// Check if user is in verification step
if (isset($_SESSION['pending_user_id'])) {
    $step = 5;
    $verification_email_sent = true;
}

// Fetch districts for dropdown
$districts = [];
$stmt = $conn->query('SELECT id, district_number, name FROM districts ORDER BY district_number');
while ($row = $stmt->fetch()) {
    $districts[$row['id']] = $row;
}

// Philippine Valid ID types
$valid_ids = [
    'SSS ID',
    'UMID',
    'PhilHealth ID',
    'TIN ID',
    'Passport',
    'Driver\'s License',
    'Voter\'s ID',
    'PRC ID',
    'Postal ID',
    'Barangay ID',
    'Senior Citizen ID',
    'PWD ID',
    'National ID (PhilSys)',
    'School ID',
    'Company ID'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle email resend
    if (isset($_POST['resend_email']) && isset($_SESSION['pending_user_id'])) {
        $user_id = $_SESSION['pending_user_id'];
        $user_email = $_SESSION['user_email'] ?? '';
        
        if ($user_email) {
            // Get user details for resending
            $stmt = $conn->prepare('SELECT full_name, email_verification_token FROM users WHERE id = ?');
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Generate new OTP
                $otp = rand(100000, 999999);
                $now = date('Y-m-d H:i:s');
                
                // Mark previous OTPs as used
                $stmt = $conn->prepare('UPDATE user_otps SET used = 1 WHERE user_id = ? AND used = 0');
                $stmt->execute([$user_id]);
                
                // Store new OTP
                $stmt = $conn->prepare('INSERT INTO user_otps (user_id, otp_code, expires_at, used, created_at) VALUES (?, ?, ?, 0, ?)');
                $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                $stmt->execute([$user_id, (string)$otp, $expires, $now]);
                
                // Send email (optimized)
                try {
                    $mailResult = sendVerificationEmail($user_email, $user['full_name'], $user['email_verification_token'], $otp);
                    $success_message = 'Verification email resent successfully!';
                } catch (Exception $e) {
                    $success_message = 'Verification email is being sent. Please check your inbox.';
                    error_log("Resend email failed: " . $e->getMessage());
                }
            } else {
                $errors[] = 'User not found.';
            }
        } else {
            $errors[] = 'No email address found.';
        }
        $step = 5;
    }
    // Handle OTP verification (Step 5)
    else if (isset($_POST['verify_otp']) && isset($_SESSION['pending_user_id'])) {
        $otp = trim($_POST['otp']);
        $user_id = $_SESSION['pending_user_id'];
        
        // Debug: Let's see what OTPs exist for this user
        $stmt = $conn->prepare('SELECT id, otp_code, expires_at, used, created_at FROM user_otps WHERE user_id = ? ORDER BY id DESC LIMIT 5');
        $stmt->execute([$user_id]);
        $allOtps = $stmt->fetchAll();
        
        // Simple OTP validation - try multiple approaches
        $otpRow = null;
        
        // Method 1: Direct string comparison
        $stmt = $conn->prepare('SELECT * FROM user_otps WHERE user_id = ? AND otp_code = ? AND used = 0 AND expires_at > NOW() ORDER BY id DESC LIMIT 1');
        $stmt->execute([$user_id, $otp]);
        $otpRow = $stmt->fetch();
        
        // Method 2: If first method fails, try trimming stored OTP
        if (!$otpRow) {
            $stmt = $conn->prepare('SELECT * FROM user_otps WHERE user_id = ? AND TRIM(otp_code) = ? AND used = 0 AND expires_at > NOW() ORDER BY id DESC LIMIT 1');
            $stmt->execute([$user_id, $otp]);
            $otpRow = $stmt->fetch();
        }
        
        // Method 3: Try with type casting
        if (!$otpRow) {
            $stmt = $conn->prepare('SELECT * FROM user_otps WHERE user_id = ? AND CAST(otp_code AS CHAR) = CAST(? AS CHAR) AND used = 0 AND expires_at > NOW() ORDER BY id DESC LIMIT 1');
            $stmt->execute([$user_id, $otp]);
            $otpRow = $stmt->fetch();
        }
        
        if ($otpRow) {
            // Mark OTP as used
            $stmt = $conn->prepare('UPDATE user_otps SET used = 1 WHERE id = ?');
            $stmt->execute([$otpRow['id']]);
            // Mark user as verified and active
            $stmt = $conn->prepare('UPDATE users SET is_email_verified = 1, email_verified_at = NOW(), status = "active" WHERE id = ?');
            $stmt->execute([$user_id]);
            
            unset($_SESSION['pending_user_id']);
            unset($_SESSION['user_email']);
            $success = true;
            $step = 6; // Completion step
            echo '<script>if(typeof clearFormData === "function") clearFormData();</script>';
        } else {
            // Enhanced error message with detailed debugging info
            $debugInfo = '';
            if (!empty($allOtps)) {
                $debugInfo = ' [Debug: Found ' . count($allOtps) . ' OTP(s) for user]';
                foreach ($allOtps as $otpDebug) {
                    $isExpired = strtotime($otpDebug['expires_at']) < time();
                    $debugInfo .= " [OTP: " . $otpDebug['otp_code'] . ", Used: " . ($otpDebug['used'] ? 'Yes' : 'No') . ", Expired: " . ($isExpired ? 'Yes' : 'No') . "]";
                }
                
                // More detailed debugging
                $latestOtp = $allOtps[0]; // Most recent OTP
                $debugInfo .= " [Input: '$otp' (" . strlen($otp) . " chars)]";
                $debugInfo .= " [DB: '" . $latestOtp['otp_code'] . "' (" . strlen($latestOtp['otp_code']) . " chars)]";
                $debugInfo .= " [Strict Compare: " . ($otp === $latestOtp['otp_code'] ? 'MATCH' : 'NO MATCH') . "]";
                $debugInfo .= " [Loose Compare: " . ($otp == $latestOtp['otp_code'] ? 'MATCH' : 'NO MATCH') . "]";
                
                // Try manual validation as a test
                if ($otp === trim($latestOtp['otp_code']) && $latestOtp['used'] == 0) {
                    $currentTime = date('Y-m-d H:i:s');
                    if ($latestOtp['expires_at'] > $currentTime) {
                        // This should work - let's force it
                        $stmt = $conn->prepare('UPDATE user_otps SET used = 1 WHERE id = ?');
                        $stmt->execute([$latestOtp['id']]);
                        
                        $stmt = $conn->prepare('UPDATE users SET is_email_verified = 1, email_verified_at = NOW(), status = "active" WHERE id = ?');
                        $stmt->execute([$user_id]);
                        
                        unset($_SESSION['pending_user_id']);
                        unset($_SESSION['user_email']);
                        $success = true;
                        $step = 6;
                        $debugInfo = ' [MANUAL VERIFICATION SUCCESSFUL]';
                    }
                }
            }
            
            if ($step == 5) { // Only show error if manual verification didn't work
                $errors[] = 'Invalid or expired OTP. Please check your email for the latest verification code.' . $debugInfo;
            }
        }
    } 
    // Handle initial registration (Steps 1-3)
    else {
        $registration_type = $_POST['registration_type'] ?? 'citizen';
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $full_name = trim($_POST['full_name'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $birthdate = $_POST['birthdate'] ?? '';
        $mobile_number = trim($_POST['mobile_number'] ?? '');
        $gender = $_POST['gender'] ?? '';
        $civil_status = $_POST['civil_status'] ?? '';
        $nationality = trim($_POST['nationality'] ?? '');
        $district_id = $_POST['district_id'] ?? '';
        $barangay_id = $_POST['barangay_id'] ?? '';
        $current_address = trim($_POST['current_address'] ?? '');
        $zip_code = trim($_POST['zip_code'] ?? '');
        $valid_id_type = $_POST['valid_id_type'] ?? '';
        
        // Handle file uploads
        $valid_id_front_image = '';
        $valid_id_back_image = '';
        $selfie_with_id_image = '';
        
        // Upload Valid ID Front
        if (isset($_FILES['valid_id_front_image']) && $_FILES['valid_id_front_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../uploads/id_images/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_ext = strtolower(pathinfo($_FILES['valid_id_front_image']['name'], PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($file_ext, $allowed_types)) {
                $errors[] = 'Invalid file type for ID front. Only JPG, JPEG, and PNG are allowed.';
            } elseif ($_FILES['valid_id_front_image']['size'] > $max_size) {
                $errors[] = 'ID front file size too large. Maximum 5MB allowed.';
            } else {
                $filename = 'id_front_' . uniqid() . '_' . time() . '.' . $file_ext;
                $upload_path = $upload_dir . $filename;
                if (move_uploaded_file($_FILES['valid_id_front_image']['tmp_name'], $upload_path)) {
                    $valid_id_front_image = '../uploads/id_images/' . $filename;
                } else {
                    $errors[] = 'Failed to upload ID front image.';
                }
            }
        }
        
        // Upload Valid ID Back
        if (isset($_FILES['valid_id_back_image']) && $_FILES['valid_id_back_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../uploads/id_images/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_ext = strtolower(pathinfo($_FILES['valid_id_back_image']['name'], PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($file_ext, $allowed_types)) {
                $errors[] = 'Invalid file type for ID back. Only JPG, JPEG, and PNG are allowed.';
            } elseif ($_FILES['valid_id_back_image']['size'] > $max_size) {
                $errors[] = 'ID back file size too large. Maximum 5MB allowed.';
            } else {
                $filename = 'id_back_' . uniqid() . '_' . time() . '.' . $file_ext;
                $upload_path = $upload_dir . $filename;
                if (move_uploaded_file($_FILES['valid_id_back_image']['tmp_name'], $upload_path)) {
                    $valid_id_back_image = '../uploads/id_images/' . $filename;
                } else {
                    $errors[] = 'Failed to upload ID back image.';
                }
            }
        }
        
        // Upload Selfie with ID
        if (isset($_FILES['selfie_with_id_image']) && $_FILES['selfie_with_id_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../uploads/id_images/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_ext = strtolower(pathinfo($_FILES['selfie_with_id_image']['name'], PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($file_ext, $allowed_types)) {
                $errors[] = 'Invalid file type for selfie. Only JPG, JPEG, and PNG are allowed.';
            } elseif ($_FILES['selfie_with_id_image']['size'] > $max_size) {
                $errors[] = 'Selfie file size too large. Maximum 5MB allowed.';
            } else {
                $filename = 'selfie_' . uniqid() . '_' . time() . '.' . $file_ext;
                $upload_path = $upload_dir . $filename;
                if (move_uploaded_file($_FILES['selfie_with_id_image']['tmp_name'], $upload_path)) {
                    $selfie_with_id_image = '../uploads/id_images/' . $filename;
                } else {
                    $errors[] = 'Failed to upload selfie image.';
                }
            }
        }


    // Validation
    if (!$username || !$email || !$full_name || !$password || !$confirm_password || !$birthdate || !$mobile_number || !$gender || !$civil_status || !$nationality || !$district_id || !$barangay_id || !$current_address || !$zip_code || !$valid_id_type) {
        $errors[] = 'All fields are required.';
    }
    if (empty($valid_id_front_image) && (!isset($_FILES['valid_id_front_image']) || $_FILES['valid_id_front_image']['error'] !== UPLOAD_ERR_OK)) {
        $errors[] = 'Please upload the front side of your valid ID.';
    }
    if (empty($valid_id_back_image) && (!isset($_FILES['valid_id_back_image']) || $_FILES['valid_id_back_image']['error'] !== UPLOAD_ERR_OK)) {
        $errors[] = 'Please upload the back side of your valid ID.';
    }
    if (empty($selfie_with_id_image) && (!isset($_FILES['selfie_with_id_image']) || $_FILES['selfie_with_id_image']['error'] !== UPLOAD_ERR_OK)) {
        $errors[] = 'Please upload a selfie with your ID.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address.';
    }
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }
    // TODO: SECURITY - Enhanced password validation (revert to simple for development)
    if (strlen($password) < 6) { // Changed from 8 for development
        $errors[] = 'Password must be at least 6 characters.';
    }
    // TODO: SECURITY - Complex password requirements (comment out for development)
    // if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
    //     $errors[] = 'Password must contain at least one uppercase letter, one lowercase letter, and one number.';
    // }
    if (!preg_match('/^09\d{9}$/', $mobile_number)) {
        $errors[] = 'Mobile number must be in Philippine format (09xxxxxxxxx).';
    }
    if (!DateTime::createFromFormat('Y-m-d', $birthdate)) {
        $errors[] = 'Invalid birthdate format.';
    }

    // TODO: SECURITY - Generic error message to prevent user enumeration
    $stmt = $conn->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        $errors[] = 'Username or email already exists.'; // Reverted from generic message for development
    }

    if (empty($errors)) {
        // TODO: SECURITY - Use Argon2id for stronger password hashing (revert to default for development)
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        // TODO: SECURITY - Argon2id configuration (comment out for development)
        // $password_hash = password_hash($password, PASSWORD_ARGON2ID, [
        //     'memory_cost' => 65536, // 64 MB
        //     'time_cost' => 4,       // 4 iterations
        //     'threads' => 3          // 3 threads
        // ]);
        $token = randomToken(32);
        $otp = rand(100000, 999999);
        $now = date('Y-m-d H:i:s');
        $stmt = $conn->prepare('INSERT INTO users (username, email, full_name, password_hash, birthdate, mobile_number, gender, civil_status, nationality, district_id, barangay_id, current_address, zip_code, valid_id_type, valid_id_front_image, valid_id_back_image, selfie_with_id_image, id_verification_status, status, is_email_verified, email_verification_token, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $username,
            $email,
            $full_name,
            $password_hash,
            $birthdate,
            $mobile_number,
            $gender,
            $civil_status,
            $nationality,
            $district_id,
            $barangay_id,
            $current_address,
            $zip_code,
            $valid_id_type,
            $valid_id_front_image,
            $valid_id_back_image,
            $selfie_with_id_image,
            'pending',
            'inactive',
            0,
            $token,
            $now,
            $now
        ]);
        $user_id = $conn->lastInsertId();
        
        // Assign role based on registration type
        $role_id = null;
        $subsystem_id = null;
        $subsystem_role_id = null;
        
        // Map registration types to subsystems and roles
        $registration_mapping = [
            'citizen' => ['role_id' => 2], // Global citizen role
            'applicant' => ['subsystem_id' => 8, 'role_name' => 'Applicant'], // Housing
            'utility_customer' => ['subsystem_id' => 2, 'role_name' => 'Customer'], // Utility
            'facility_user' => ['subsystem_id' => 4, 'role_name' => 'Citizen'], // Public Facilities
            'resident' => ['subsystem_id' => 5, 'role_name' => 'Resident'], // Community Infrastructure
            'road_resident' => ['subsystem_id' => 3, 'role_name' => 'Resident'], // Road and Transportation
            'land_citizen' => ['subsystem_id' => 7, 'role_name' => 'Citizen'], // Land Registration and Titling
            'property_owner' => ['subsystem_id' => 6, 'role_name' => 'Property Owner'] // Urban Planning & Development
        ];
        
        if (isset($registration_mapping[$registration_type])) {
            $mapping = $registration_mapping[$registration_type];
            
            if (isset($mapping['role_id'])) {
                // Global role assignment (like citizen)
                $role_id = $mapping['role_id'];
            } else {
                // Subsystem role assignment
                $subsystem_id = $mapping['subsystem_id'];
                
                // Get the appropriate subsystem role
                $stmt = $conn->prepare('SELECT id FROM subsystem_roles WHERE subsystem_id = ? AND role_name = ?');
                $stmt->execute([$subsystem_id, $mapping['role_name']]);
                $subsystem_role = $stmt->fetch();
                if ($subsystem_role) {
                    $subsystem_role_id = $subsystem_role['id'];
                }
            }
        }
        
        // Update user with role assignments
        $stmt = $conn->prepare('UPDATE users SET role_id = ?, subsystem_id = ?, subsystem_role_id = ? WHERE id = ?');
        $stmt->execute([$role_id, $subsystem_id, $subsystem_role_id, $user_id]);
        

        // Store OTP
        $stmt = $conn->prepare('INSERT INTO user_otps (user_id, otp_code, expires_at, used, created_at) VALUES (?, ?, ?, 0, ?)');
        $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        $stmt->execute([$user_id, (string)$otp, $expires, $now]);
        // Send email (optimized for speed)
        try {
            // Send email in background for faster response
            $mailResult = sendVerificationEmail($email, $full_name, $token, $otp);
            
            // Always proceed to verification step - email will be sent
            $_SESSION['pending_user_id'] = $user_id;
            $_SESSION['user_email'] = $email;
            
            // TODO: SECURITY - Regenerate session ID for security (comment out for development)
            // session_regenerate_id(true);
            
            $verification_email_sent = true;
            $step = 5; // Move to verification step
            
            // Log email attempt
            error_log("Email verification sent to: $email for user: $user_id");
            
            // Redirect to show step 5
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        } catch (Exception $e) {
            // Still proceed but log the error
            $_SESSION['pending_user_id'] = $user_id;
            $_SESSION['user_email'] = $email;
            $verification_email_sent = true;
            $step = 5;
            error_log("Email send failed but proceeding: " . $e->getMessage());
            
            // Redirect to show step 5
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }
    }
    } // Close else block for registration logic
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - LGU1</title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f2f7f5;
            --headline: #00473e;
            --paragraph: #475d5b;
            --button: #faae2b;
            --button-text: #00473e;
            --stroke: #00332c;
            --highlight: #faae2b;
            --secondary: #ffa8ba;
            --tertiary: #fa5246;
        }
        .background-radial-gradient {
            background-color: var(--bg-color);
            background-image: radial-gradient(650px circle at 0% 0%,
                rgba(0, 71, 62, 0.3) 15%,
                rgba(0, 71, 62, 0.2) 35%,
                rgba(242, 247, 245, 0.8) 75%,
                rgba(242, 247, 245, 0.9) 80%,
                transparent 100%),
              radial-gradient(1250px circle at 100% 100%,
                rgba(250, 174, 43, 0.2) 15%,
                rgba(255, 168, 186, 0.15) 35%,
                rgba(242, 247, 245, 0.8) 75%,
                rgba(242, 247, 245, 0.9) 80%,
                transparent 100%);
            min-height: 100vh;
            font-family: 'Inter', Arial, sans-serif;
            overflow: hidden;
        }
        #radius-shape-1 {
            height: 220px;
            width: 220px;
            top: -60px;
            left: -130px;
            background: radial-gradient(var(--highlight), var(--secondary));
            overflow: hidden;
        }
        #radius-shape-2 {
            border-radius: 38% 62% 63% 37% / 70% 33% 67% 30%;
            bottom: -60px;
            right: -110px;
            width: 300px;
            height: 300px;
            background: radial-gradient(var(--highlight), var(--secondary));
            overflow: hidden;
        }
        .bg-glass {
            background-color: hsla(0, 0%, 100%, 0.9) !important;
            backdrop-filter: saturate(200%) blur(25px);
        }
        .hero-text {
            color: var(--headline);
        }
        .hero-text span {
            color: var(--highlight);
        }
        .hero-description {
            color: var(--paragraph);
            opacity: 0.8;
        }
        .gov-banner {
            background: linear-gradient(90deg, <?= $colors['highlight'] ?> 0%, <?= $colors['secondary'] ?> 100%);
            color: <?= $colors['button_text'] ?>;
            font-family: 'Merriweather', serif;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-align: center;
            padding: 0.7rem 0;
            border-radius: 0 0 1.5rem 1.5rem;
            box-shadow: 0 2px 8px #00473e22;
            margin-bottom: 1.5rem;
        }
        .gov-seal {
            display: block;
            margin: 0 auto 1rem auto;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #fff;
            border: 4px solid <?= $colors['highlight'] ?>;
            box-shadow: 0 2px 8px #00473e22;
            position: relative;
        }
        .gov-seal svg {
            width: 100%;
            height: 100%;
            display: block;
        }
        .register-container {
            background: rgba(255,255,255,0.98);
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(0,71,62,0.10), 0 1.5px 8px 0 #00473e22;
            max-width: 550px;
            width: 100%;
            margin: 0 auto;
            padding: 3rem 3.5rem;
            border-top: 8px solid var(--highlight);
            border-bottom: 4px solid var(--secondary);
            border-left: 2px solid var(--headline)22;
            border-right: 2px solid var(--headline)22;
            position: relative;
            backdrop-filter: blur(10px);
            overflow: hidden;
        }
        .register-container::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, var(--highlight), var(--secondary), var(--button));
            border-radius: 1.5rem;
            z-index: -1;
            opacity: 0.3;
        }
        .step {
            display: none;
        }
        .step.active {
            display: block;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }
        .step-indicator .step-item {
            display: flex;
            align-items: center;
            color: #ccc;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .step-indicator .step-item.active {
            color: <?= $colors['headline'] ?>;
        }
        .step-indicator .step-item.completed {
            color: <?= $colors['button'] ?>;
        }
        .step-indicator .step-number {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 6px;
            font-weight: 700;
            font-size: 0.7rem;
        }
        .step-indicator .step-item.active .step-number {
            background: <?= $colors['headline'] ?>;
            color: white;
        }
        .step-indicator .step-item.completed .step-number {
            background: <?= $colors['button'] ?>;
            color: <?= $colors['button_text'] ?>;
        }
        .step-indicator .step-line {
            width: 30px;
            height: 2px;
            background: #e9ecef;
            margin: 0 6px;
            margin-top: 12px;
        }
        .step-indicator .step-line.completed {
            background: <?= $colors['button'] ?>;
        }
        .step-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5rem;
            gap: 1rem;
        }
        .btn-step {
            flex: 1;
        }
        .form-control.is-invalid {
            border-color: <?= $colors['tertiary'] ?>;
            box-shadow: 0 0 0 0.2rem <?= $colors['tertiary'] ?>33;
        }
        .id-upload-container {
            border: 2px dashed <?= $colors['highlight'] ?>;
            border-radius: 8px;
            padding: 1rem;
            background: <?= $colors['background'] ?>22;
        }
        #imagePreview img {
            max-height: 200px;
            object-fit: contain;
        }
        .register-logo {
            display: block;
            margin: 0 auto 1.2rem auto;
            max-width: 90px;
            filter: drop-shadow(0 2px 8px #00473e22);
        }
        .gov-subtitle {
            text-align: center;
            color: var(--paragraph);
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            letter-spacing: 0.5px;
        }
        .form-label {
            color: var(--headline);
            font-family: 'Merriweather', serif;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .form-control {
            transition: all 0.3s ease;
            border: 2px solid #e9ecef;
        }
        .form-control:focus {
            border-color: var(--highlight);
            box-shadow: 0 0 0 0.2rem var(--highlight)33, 0 0 20px rgba(250, 174, 43, 0.1);
            transform: translateY(-2px);
        }
        .input-group-text {
            background: var(--bg-color);
            color: var(--headline);
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--button) 0%, var(--highlight) 100%);
            color: var(--button-text);
            border: none;
            font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(250, 174, 43, 0.3);
            transition: all 0.3s ease;
            font-family: 'Merriweather', serif;
            position: relative;
            overflow: hidden;
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        .btn-primary:hover::before {
            left: 100%;
        }
        .btn-primary:hover, .btn-primary:focus {
            background: linear-gradient(135deg, var(--highlight) 0%, var(--secondary) 100%);
            color: white;
            box-shadow: 0 6px 20px rgba(250, 174, 43, 0.4);
            transform: translateY(-2px);
        }
        .loader {
            display: none;
            border: 4px solid #f3f3f3;
            border-top: 4px solid <?= $colors['headline'] ?>;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem auto;
        }
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0 0.5rem 0;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1.5px solid #e0e0e0;
        }
        .divider:not(:empty)::before {
            margin-right: .75em;
        }
        .divider:not(:empty)::after {
            margin-left: .75em;
        }
        .login-link {
            color: <?= $colors['headline'] ?>;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }
        .login-link:hover {
            color: <?= $colors['tertiary'] ?>;
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .hero-text {
                font-size: 2rem !important;
                text-align: center;
            }
            .hero-description {
                text-align: center;
            }
            #radius-shape-1, #radius-shape-2 {
                display: none;
            }
            .step-indicator {
                flex-wrap: wrap;
                gap: 0.3rem;
                justify-content: center;
            }
            .step-indicator .step-line {
                width: 15px;
                margin: 0 3px;
            }
            .step-indicator .step-item {
                font-size: 0.8rem;
            }
        }
        @media (max-width: 480px) {
            .step-indicator .step-item span {
                display: none;
            }
            .step-indicator .step-number {
                margin-right: 0;
                width: 25px;
                height: 25px;
                font-size: 0.7rem;
            }
            .step-indicator .step-line {
                width: 10px;
                margin: 0 2px;
            }
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Registration tabs styling */
        .nav-pills .nav-link {
            border-radius: 8px;
            margin: 0 4px;
            transition: all 0.3s ease;
        }
        
        .nav-pills .nav-link.active {
            background: <?= $colors['button'] ?> !important;
            color: <?= $colors['button_text'] ?> !important;
            border: none !important;
        }
        
        .nav-pills .nav-link:not(.active) {
            background: transparent;
            border: 2px solid <?= $colors['button'] ?>;
            color: <?= $colors['button'] ?>;
        }
        
        .nav-pills .nav-link:not(.active):hover {
            background: <?= $colors['button'] ?>22;
            border-color: <?= $colors['highlight'] ?>;
            color: <?= $colors['headline'] ?>;
        }
        .email-loader-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .email-loader-content {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            text-align: center;
            min-width: 300px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        .email-loader-spinner {
            display: inline-block;
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid <?= $colors['headline'] ?>;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 1rem;
        }
        
        #getCurrentLocation {
            border-left: none;
        }
        
        #getCurrentLocation:hover {
            background: <?= $colors['button'] ?>;
            color: <?= $colors['button_text'] ?>;
            border-color: <?= $colors['button'] ?>;
        }
        
        #locationStatus {
            transition: all 0.3s ease;
        }
        
        .id-upload-container .form-control.is-invalid {
            border-color: <?= $colors['tertiary'] ?>;
        }
        
        .id-upload-container .invalid-feedback {
            display: none;
            color: <?= $colors['tertiary'] ?>;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .id-upload-container .form-control.is-invalid ~ .invalid-feedback {
            display: block;
        }
    </style>
</head>
<body>
    <section class="background-radial-gradient overflow-hidden">
        <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
            <div class="row gx-lg-5 align-items-center mb-5">
                <div class="col-lg-6 mb-5 mb-lg-0" style="z-index: 10">
                    <h1 class="my-5 display-5 fw-bold ls-tight hero-text">
                        Local Government Unit 1 <br />
                        <span>Registration Portal</span>
                    </h1>
                    <p class="mb-4 hero-description">
                        Create your account to access LGU1 services and systems. Join our digital government platform for efficient service delivery.
                    </p>
                </div>

                <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
                    <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                    <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

                    <div class="card bg-glass">
                        <div class="card-body px-4 py-5 px-md-5">
                            <div class="text-center mb-4">
                                <img src="assets/images/logo.png" alt="LGU1 Logo" style="max-width: 80px; margin-bottom: 1rem;">
                                <h2 style="color: var(--headline); font-family: 'Merriweather', serif; font-weight: 700;">Create Account</h2>
                                <p class="text-muted"><i class="bi bi-people-fill"></i> For Official Use of LGU1</p>
                            </div>
            

            
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step-item active" id="step-indicator-1">
                    <div class="step-number">1</div>
                    <span>Account</span>
                </div>
                <div class="step-line" id="step-line-1"></div>
                <div class="step-item" id="step-indicator-2">
                    <div class="step-number">2</div>
                    <span>Personal</span>
                </div>
                <div class="step-line" id="step-line-2"></div>
                <div class="step-item" id="step-indicator-3">
                    <div class="step-number">3</div>
                    <span>Address</span>
                </div>
                <div class="step-line" id="step-line-3"></div>
                <div class="step-item" id="step-indicator-4">
                    <div class="step-number">4</div>
                    <span>ID</span>
                </div>
                <div class="step-line" id="step-line-4"></div>
                <div class="step-item" id="step-indicator-5">
                    <div class="step-number">5</div>
                    <span>Verify</span>
                </div>
            </div>
            
            <div class="loader" id="loader"></div>
            <?php if ($success && $step === 6): ?>
                <script>
                    window.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Registration Complete!',
                            text: 'Your account has been successfully verified and activated.',
                            confirmButtonColor: '<?= $colors['button'] ?>',
                        });
                    });
                </script>
            <?php endif; ?>
            <?php if ($verification_email_sent && $step === 5): ?>
                <script>
                    window.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'info',
                            title: 'Check Your Email',
                            text: 'We\'ve sent a verification code to your email address.',
                            confirmButtonColor: '<?= $colors['button'] ?>',
                        });
                    });
                </script>
            <?php endif; ?>
            <?php if (isset($success_message)): ?>
                <script>
                    window.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Email Sent!',
                            text: '<?= htmlspecialchars($success_message) ?>',
                            confirmButtonColor: '<?= $colors['button'] ?>',
                        });
                    });
                </script>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <script>
                    window.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Registration Failed',
                            html: '<?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>',
                            confirmButtonColor: '<?= $colors['button'] ?>',
                        });
                    });
                </script>
            <?php endif; ?>
            <form method="post" id="registerForm" autocomplete="off" novalidate enctype="multipart/form-data">
                <!-- Registration Type Selection -->
                <div class="mb-4">
                    <label for="registration_type_form" class="form-label" style="color: <?= $colors['headline'] ?>; font-family: 'Merriweather', serif; font-weight: 700;"><i class="bi bi-person-check"></i> Registration Type</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-list-ul"></i></span>
                        <select class="form-control" id="registration_type_form" name="registration_type" required>
                            <option value="applicant">Housing Applicant - Housing & resettlement programs</option>
                            <option value="utility_customer">Utility Customer - Water & electricity billing</option>
                            <option value="facility_user">Citizen - Public facilities reservation</option>
                            <option value="land_citizen">Citizen - Land Registration and Titling System</option>
                            <option value="resident">Resident - Community infrastructure maintenance</option>
                            <option value="road_resident">Resident - Road and Transportation Infrastructure Monitoring</option>
                            <option value="property_owner">Property Owner - Urban Planning & Development</option>
                        </select>
                    </div>
                    <small class="text-muted">Select the type that best describes your primary need for LGU services</small>
                </div>
                
                <!-- Step 1: Account Information -->
                <div class="step active" id="step-1">
                    <h5 class="mb-3 text-center" style="color: <?= $colors['headline'] ?>;">Account Information</h5>
                    <div class="mb-3">
                        <label for="username" class="form-label"><i class="bi bi-person-badge"></i> Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="username" name="username" required maxlength="50" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label"><i class="bi bi-envelope-paper"></i> Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" required maxlength="100" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label"><i class="bi bi-lock"></i> Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" autocomplete="new-password" required minlength="6">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye" id="passwordIcon"></i>
                            </button>
                        </div>
                        <div class="password-strength mt-2" id="passwordStrength" style="display: none;">
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" id="strengthBar" style="width: 0%;"></div>
                            </div>
                            <small id="strengthText" class="text-muted">Password strength</small>
                        </div>
                        <!-- TODO: SECURITY - Enhanced password requirements (simplified for development) -->
                        <small class="text-muted">Must be 6+ characters (8+ with uppercase, lowercase, and number for production)</small>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label"><i class="bi bi-lock-fill"></i> Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" autocomplete="new-password" required minlength="6">
                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                <i class="bi bi-eye" id="confirmPasswordIcon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="step-buttons">
                        <button type="button" class="btn btn-primary btn-step" id="nextStep1">Next <i class="bi bi-arrow-right"></i></button>
                    </div>
                </div>

                <!-- Step 2: Personal Information -->
                <div class="step" id="step-2">
                    <h5 class="mb-3 text-center" style="color: <?= $colors['headline'] ?>;">Personal Information</h5>
                    <div class="mb-3">
                        <label for="full_name" class="form-label"><i class="bi bi-person-vcard"></i> Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                            <input type="text" class="form-control" id="full_name" name="full_name" required maxlength="100" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="birthdate" class="form-label"><i class="bi bi-calendar-date"></i> Birthdate</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                            <input type="date" class="form-control" id="birthdate" name="birthdate" required value="<?= htmlspecialchars($_POST['birthdate'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="mobile_number" class="form-label"><i class="bi bi-phone"></i> Mobile Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-phone"></i></span>
                            <input type="tel" class="form-control" id="mobile_number" name="mobile_number" required placeholder="09xxxxxxxxx" pattern="^09\d{9}$" maxlength="11" value="<?= htmlspecialchars($_POST['mobile_number'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label"><i class="bi bi-gender-ambiguous"></i> Gender</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male" <?= ($_POST['gender'] ?? '') == 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= ($_POST['gender'] ?? '') == 'female' ? 'selected' : '' ?>>Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="civil_status" class="form-label"><i class="bi bi-heart"></i> Civil Status</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-hearts"></i></span>
                            <select class="form-control" id="civil_status" name="civil_status" required>
                                <option value="">Select Civil Status</option>
                                <option value="single" <?= ($_POST['civil_status'] ?? '') == 'single' ? 'selected' : '' ?>>Single</option>
                                <option value="married" <?= ($_POST['civil_status'] ?? '') == 'married' ? 'selected' : '' ?>>Married</option>
                                <option value="divorced" <?= ($_POST['civil_status'] ?? '') == 'divorced' ? 'selected' : '' ?>>Divorced</option>
                                <option value="widowed" <?= ($_POST['civil_status'] ?? '') == 'widowed' ? 'selected' : '' ?>>Widowed</option>
                                <option value="separated" <?= ($_POST['civil_status'] ?? '') == 'separated' ? 'selected' : '' ?>>Separated</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="nationality" class="form-label"><i class="bi bi-flag"></i> Nationality</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-globe"></i></span>
                            <input type="text" class="form-control" id="nationality" name="nationality" required maxlength="50" value="<?= htmlspecialchars($_POST['nationality'] ?? 'Filipino') ?>">
                        </div>
                    </div>
                    <div class="step-buttons">
                        <button type="button" class="btn btn-outline-secondary btn-step" id="prevStep2"><i class="bi bi-arrow-left"></i> Previous</button>
                        <button type="button" class="btn btn-primary btn-step" id="nextStep2">Next <i class="bi bi-arrow-right"></i></button>
                    </div>
                </div>

                <!-- Step 3: Address Information -->
                <div class="step" id="step-3">
                    <h5 class="mb-3 text-center" style="color: <?= $colors['headline'] ?>;">Address Information</h5>
                    <div class="mb-3">
                        <label for="district_id" class="form-label"><i class="bi bi-geo-alt"></i> District</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-map"></i></span>
                            <select class="form-control" id="district_id" name="district_id" required>
                                <option value="">Select District</option>
                                <?php foreach ($districts as $id => $district): ?>
                                    <option value="<?= $id ?>" <?= ($_POST['district_id'] ?? '') == $id ? 'selected' : '' ?>>
                                        District <?= $district['district_number'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="barangay_id" class="form-label"><i class="bi bi-house"></i> Barangay</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-house"></i></span>
                            <select class="form-control" id="barangay_id" name="barangay_id" required>
                                <option value="">Select Barangay</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="current_address" class="form-label"><i class="bi bi-geo"></i> Current Residential Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-house-door"></i></span>
                            <textarea class="form-control" id="current_address" name="current_address" required rows="3" placeholder="Enter your complete residential address"><?= htmlspecialchars($_POST['current_address'] ?? '') ?></textarea>
                            <button class="btn btn-outline-primary" type="button" id="getCurrentLocation" title="Get current location">
                                <i class="bi bi-geo-alt-fill"></i>
                            </button>
                        </div>
                        <small class="text-muted">Click the location button to automatically detect your current address</small>
                        <div id="locationStatus" class="mt-2" style="display: none;">
                            <small class="text-info"><i class="bi bi-hourglass-split"></i> Getting your location...</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="zip_code" class="form-label"><i class="bi bi-mailbox"></i> Zip Code</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="text" class="form-control" id="zip_code" name="zip_code" required maxlength="10" pattern="[0-9]{4,10}" placeholder="e.g. 1100" value="<?= htmlspecialchars($_POST['zip_code'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="step-buttons">
                        <button type="button" class="btn btn-outline-secondary btn-step" id="prevStep3"><i class="bi bi-arrow-left"></i> Previous</button>
                        <button type="button" class="btn btn-primary btn-step" id="nextStep3">Next <i class="bi bi-arrow-right"></i></button>
                    </div>
                </div>

                <!-- Step 4: ID Verification -->
                <div class="step" id="step-4">
                    <h5 class="mb-3 text-center" style="color: <?= $colors['headline'] ?>;">ID Verification</h5>
                    <div class="mb-3">
                        <label for="valid_id_type" class="form-label"><i class="bi bi-card-text"></i> Valid ID Type</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                            <select class="form-control" id="valid_id_type" name="valid_id_type" required>
                                <option value="">Select Valid ID</option>
                                <?php foreach ($valid_ids as $id_type): ?>
                                    <option value="<?= htmlspecialchars($id_type) ?>" <?= ($_POST['valid_id_type'] ?? '') == $id_type ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($id_type) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-card-image"></i> Upload Valid ID - Front Side</label>
                        <div class="id-upload-container">
                            <input type="file" class="form-control" id="valid_id_front_image" name="valid_id_front_image" accept="image/*" required>
                            <small class="text-muted">Upload front side (JPG, PNG, max 5MB)</small>
                            <div class="invalid-feedback" id="frontImageError">Please upload the front side of your ID</div>
                            <!-- Preview Area for Front -->
                            <div id="frontImagePreview" style="display: none;">
                                <img id="frontPreviewImg" src="" alt="ID Front Preview" style="max-width: 100%; height: auto; border-radius: 8px; border: 2px solid <?= $colors['highlight'] ?>; margin-top: 10px;">
                                <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="removeFrontImage">
                                    <i class="bi bi-trash"></i> Remove Front Image
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-card-image"></i> Upload Valid ID - Back Side</label>
                        <div class="id-upload-container">
                            <input type="file" class="form-control" id="valid_id_back_image" name="valid_id_back_image" accept="image/*" required>
                            <small class="text-muted">Upload back side (JPG, PNG, max 5MB)</small>
                            <div class="invalid-feedback" id="backImageError">Please upload the back side of your ID</div>
                            <!-- Preview Area for Back -->
                            <div id="backImagePreview" style="display: none;">
                                <img id="backPreviewImg" src="" alt="ID Back Preview" style="max-width: 100%; height: auto; border-radius: 8px; border: 2px solid <?= $colors['highlight'] ?>; margin-top: 10px;">
                                <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="removeBackImage">
                                    <i class="bi bi-trash"></i> Remove Back Image
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="selfie_with_id_image" class="form-label"><i class="bi bi-person-circle"></i> Selfie with ID</label>
                        <div class="id-upload-container">
                            <input type="file" class="form-control" id="selfie_with_id_image" name="selfie_with_id_image" accept="image/*" required>
                            <small class="text-muted">Upload selfie with ID (JPG, PNG, max 5MB)</small>
                            <div class="invalid-feedback" id="selfieError">Please upload a selfie with your ID</div>
                            <!-- Selfie Preview Area -->
                            <div id="selfiePreview" style="display: none;">
                                <img id="selfieImg" src="" alt="Selfie Preview" style="max-width: 100%; height: auto; border-radius: 8px; border: 2px solid <?= $colors['button'] ?>; margin-top: 10px;">
                                <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="removeSelfie">
                                    <i class="bi bi-trash"></i> Remove Selfie
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Camera Modal for Front ID -->
                    <div class="modal fade" id="frontCameraModal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Capture ID Front Side</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <p class="text-muted mb-3">Position the front side of your ID within the camera frame</p>
                                    <video id="frontCameraVideo" width="100%" style="max-width: 400px; border-radius: 8px;" autoplay></video>
                                    <canvas id="frontCameraCanvas" style="display: none;"></canvas>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="frontCaptureBtn">
                                        <i class="bi bi-camera"></i> Capture Front
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Camera Modal for Back ID -->
                    <div class="modal fade" id="backCameraModal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Capture ID Back Side</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <p class="text-muted mb-3">Position the back side of your ID within the camera frame</p>
                                    <video id="backCameraVideo" width="100%" style="max-width: 400px; border-radius: 8px;" autoplay></video>
                                    <canvas id="backCameraCanvas" style="display: none;"></canvas>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="backCaptureBtn">
                                        <i class="bi bi-camera"></i> Capture Back
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Camera Modal for Selfie -->
                    <div class="modal fade" id="selfieCameraModal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Take Selfie with ID</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <p class="text-muted mb-3">Hold your ID next to your face and take a selfie</p>
                                    <video id="selfieCameraVideo" width="100%" style="max-width: 400px; border-radius: 8px;" autoplay></video>
                                    <canvas id="selfieCameraCanvas" style="display: none;"></canvas>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-success" id="selfieCaptureBtn">
                                        <i class="bi bi-camera-fill"></i> Capture Selfie
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="step-buttons">
                        <button type="button" class="btn btn-outline-secondary btn-step" id="prevStep4"><i class="bi bi-arrow-left"></i> Previous</button>
                        <button type="button" class="btn btn-primary btn-step" id="nextStep4"><i class="bi bi-person-plus"></i> Register</button>
                    </div>
                </div>

                <!-- Step 5: Email Verification -->
                <div class="step" id="step-5">
                    <h5 class="mb-3 text-center" style="color: <?= $colors['headline'] ?>;">Email Verification</h5>
                    <?php if ($verification_email_sent): ?>
                        <div class="alert alert-info text-center">
                            <i class="bi bi-envelope-check"></i> We've sent a verification code to<br>
                            <strong><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></strong>
                        </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="otp" class="form-label"><i class="bi bi-shield-check"></i> Enter Verification Code</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-123"></i></span>
                            <input type="text" class="form-control" id="otp" name="otp" required maxlength="6" pattern="[0-9]{6}" placeholder="Enter 6-digit code" autocomplete="off">
                        </div>
                        <small class="text-muted">Check your email for the 6-digit verification code</small>
                    </div>
                    <div class="text-center mb-3">
                        <button type="submit" class="btn btn-outline-info btn-sm" name="resend_email" value="1" id="resendEmailBtn">
                            <i class="bi bi-arrow-clockwise"></i> Resend Verification Email
                        </button>
                        <br><small class="text-muted">Didn't receive the email? Click above to resend.</small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="termsAccepted" name="terms_accepted" required>
                            <label class="form-check-label" for="termsAccepted">
                                I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a> and <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Data Privacy Policy</a>
                            </label>
                        </div>
                    </div>
                    <div class="step-buttons">
                        <button type="button" class="btn btn-outline-secondary btn-step" id="prevStep5"><i class="bi bi-arrow-left"></i> Previous</button>
                        <button type="submit" class="btn btn-primary btn-step" name="verify_otp" value="1" disabled><i class="bi bi-shield-check"></i> Verify Email</button>
                    </div>
                </div>

                <!-- Step 6: Success -->
                <div class="step" id="step-6">
                    <div class="text-center">
                        <div class="mb-4">
                            <i class="bi bi-check-circle" style="font-size: 4rem; color: <?= $colors['button'] ?>;"></i>
                        </div>
                        <h5 class="mb-3" style="color: <?= $colors['headline'] ?>;">Registration Complete!</h5>
                        <p class="text-muted mb-4">Your account has been successfully created and verified. You can now log in to access the LGU1 system.</p>
                        <a href="login.php" class="btn btn-primary" onclick="clearFormData()"><i class="bi bi-box-arrow-in-right"></i> Go to Login</a>
                    </div>
                </div>
                <?php if ($step < 5): ?>
                <div class="divider">or</div>
                <div class="text-center">
                    <a href="login.php" class="login-link"><i class="bi bi-box-arrow-in-right"></i> Already have an account? Login</a>
                </div>
                <?php endif; ?>
            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Email Loader Modal -->
    <div class="email-loader-modal" id="emailLoaderModal">
        <div class="email-loader-content">
            <div class="email-loader-spinner"></div>
            <h5 style="color: <?= $colors['headline'] ?>; margin-bottom: 0.5rem;">Sending Verification Email</h5>
            <p style="color: <?= $colors['paragraph'] ?>; margin: 0;">Please wait while we send your verification code...</p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const registerForm = document.getElementById('registerForm');
        const loader = document.getElementById('loader');
        const btn = document.getElementById('registerBtn');
        const emailLoaderModal = document.getElementById('emailLoaderModal');
        
        // Simple localStorage for form persistence
        function saveToStorage() {
            const data = {};
            document.querySelectorAll('input, select, textarea').forEach(el => {
                if (el.name && el.type !== 'password' && el.type !== 'file') {
                    data[el.name] = el.value;
                }
            });
            data.currentStep = currentStep;
            localStorage.setItem('regData', JSON.stringify(data));
        }
        
        function loadFromStorage() {
            const saved = localStorage.getItem('regData');
            if (saved) {
                const data = JSON.parse(saved);
                Object.keys(data).forEach(key => {
                    if (key === 'currentStep' && data[key] < 5) {
                        currentStep = data[key];
                    } else {
                        const el = document.querySelector(`[name="${key}"]`);
                        if (el) {
                            el.value = data[key];
                            if (el.tagName === 'SELECT') el.dispatchEvent(new Event('change'));
                        }
                    }
                });
            }
        }
        
        function clearFormData() {
            localStorage.removeItem('regData');
        }
        
        // Handle form submission
        registerForm && registerForm.addEventListener('submit', function(e) {
            handleFormSubmission(e, this);
        });
        
        // Remove duplicate DOMContentLoaded listener
        
        function handleFormSubmission(e, form) {
            // Check if we're on step 4 (ID verification) - this means we're about to send email
            const currentActiveStep = document.querySelector('.step.active');
            const isRegistrationStep = currentActiveStep && currentActiveStep.id === 'step-4';
            const isResendEmail = e.submitter && e.submitter.name === 'resend_email';
            
            if (isRegistrationStep || isResendEmail) {
                // Show email loader modal for registration or resend email
                emailLoaderModal.style.display = 'flex';
                if (isResendEmail) {
                    // Update loader text for resend
                    emailLoaderModal.querySelector('h5').textContent = 'Resending Verification Email';
                    emailLoaderModal.querySelector('p').textContent = 'Please wait while we resend your verification code...';
                }
                
                // Auto-hide email loader after 3 seconds for better UX
                setTimeout(() => {
                    if (emailLoaderModal.style.display === 'flex') {
                        emailLoaderModal.style.display = 'none';
                    }
                }, 3000);
            } else {
                // Show regular loader for OTP verification
                loader.style.display = 'block';
            }
            
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;
            if (isResendEmail) {
                const resendBtn = document.getElementById('resendEmailBtn');
                if (resendBtn) resendBtn.disabled = true;
                // Note: Cooldown will be started after page reload via PHP check
            }
        };

        // Password strength checker
        function checkPasswordStrength(password) {
            let score = 0;
            let feedback = [];
            
            if (password.length >= 8) score += 1;
            else feedback.push('8+ characters');
            
            if (/[a-z]/.test(password)) score += 1;
            else feedback.push('lowercase letter');
            
            if (/[A-Z]/.test(password)) score += 1;
            else feedback.push('uppercase letter');
            
            if (/\d/.test(password)) score += 1;
            else feedback.push('number');
            
            if (/[^\w\s]/.test(password)) score += 1;
            
            return { score, feedback };
        }
        
        // Password input handler
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('passwordStrength');
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            
            if (password.length === 0) {
                strengthDiv.style.display = 'none';
                return;
            }
            
            strengthDiv.style.display = 'block';
            const { score, feedback } = checkPasswordStrength(password);
            
            const colors = ['#dc3545', '#fd7e14', '#ffc107', '#28a745', '#20c997'];
            const labels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
            
            strengthBar.style.width = (score * 20) + '%';
            strengthBar.style.backgroundColor = colors[score - 1] || colors[0];
            strengthText.textContent = labels[score - 1] || labels[0];
            
            if (feedback.length > 0) {
                strengthText.textContent += ' - Need: ' + feedback.join(', ');
            }
        });
        
        // Password toggle functionality
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                passwordIcon.className = 'bi bi-eye-slash';
            } else {
                passwordField.type = 'password';
                passwordIcon.className = 'bi bi-eye';
            }
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const confirmPasswordField = document.getElementById('confirm_password');
            const confirmPasswordIcon = document.getElementById('confirmPasswordIcon');
            
            if (confirmPasswordField.type === 'password') {
                confirmPasswordField.type = 'text';
                confirmPasswordIcon.className = 'bi bi-eye-slash';
            } else {
                confirmPasswordField.type = 'password';
                confirmPasswordIcon.className = 'bi bi-eye';
            }
        });

        // Multi-step form navigation
        let currentStep = <?= $step ?>;
        const totalSteps = 5;
        const phpStep = <?= $step ?>;
        
        // Resend email cooldown mechanism
        let resendCooldown = false;
        const resendBtn = document.getElementById('resendEmailBtn');
        
        function startResendCooldown() {
            if (!resendBtn) return;
            
            resendCooldown = true;
            resendBtn.disabled = true;
            let countdown = 30; // 30 seconds cooldown
            
            const originalText = resendBtn.innerHTML;
            const updateButton = () => {
                resendBtn.innerHTML = `<i class="bi bi-hourglass-split"></i> Wait ${countdown}s`;
                countdown--;
                
                if (countdown < 0) {
                    resendBtn.disabled = false;
                    resendBtn.innerHTML = originalText;
                    resendCooldown = false;
                } else {
                    setTimeout(updateButton, 1000);
                }
            };
            updateButton();
        }
        
        // Start cooldown if we're on verification step (page loaded after resend)
        <?php if (isset($success_message) && $step === 5): ?>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(startResendCooldown, 2000); // Start after SweetAlert shows
        });
        <?php endif; ?>

        function showStep(step) {
            // Hide all steps
            document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
            // Show current step
            document.getElementById(`step-${step}`).classList.add('active');
            
            // Update step indicators
            for (let i = 1; i <= totalSteps; i++) {
                const indicator = document.getElementById(`step-indicator-${i}`);
                const line = document.getElementById(`step-line-${i}`);
                
                if (i < step) {
                    indicator.classList.add('completed');
                    indicator.classList.remove('active');
                    if (line) line.classList.add('completed');
                } else if (i === step) {
                    indicator.classList.add('active');
                    indicator.classList.remove('completed');
                } else {
                    indicator.classList.remove('active', 'completed');
                    if (line) line.classList.remove('completed');
                }
            }
            
            saveToStorage();
        }

        function validateStep(step) {
            const stepElement = document.getElementById(`step-${step}`);
            const inputs = stepElement.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;
            let errorMessages = [];

            inputs.forEach(input => {
                // Special handling for file inputs
                if (input.type === 'file') {
                    if (!input.files || input.files.length === 0) {
                        input.classList.add('is-invalid');
                        isValid = false;
                        
                        // Show specific error messages
                        if (input.id === 'valid_id_front_image') {
                            errorMessages.push('Please upload the front side of your ID');
                        } else if (input.id === 'valid_id_back_image') {
                            errorMessages.push('Please upload the back side of your ID');
                        } else if (input.id === 'selfie_with_id_image') {
                            errorMessages.push('Please upload a selfie with your ID');
                        }
                    } else {
                        input.classList.remove('is-invalid');
                    }
                } else if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                    // Additional validation for specific fields
                    if (input.type === 'password' && step === 1) {
                        const password = document.getElementById('password').value;
                        const confirmPassword = document.getElementById('confirm_password').value;
                        if (password !== confirmPassword) {
                            document.getElementById('confirm_password').classList.add('is-invalid');
                            isValid = false;
                            errorMessages.push('Passwords do not match');
                        }
                    }
                }
            });

            // Show validation errors if any
            if (!isValid && errorMessages.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: errorMessages.join('<br>'),
                    confirmButtonColor: '<?= $colors['button'] ?>'
                });
            }

            return isValid;
        }

        // Next button handlers
        const nextStep1 = document.getElementById('nextStep1');
        if (nextStep1) {
            nextStep1.addEventListener('click', function() {
                if (validateStep(1)) {
                    currentStep = 2;
                    showStep(currentStep);
                }
            });
        }

        const nextStep2 = document.getElementById('nextStep2');
        if (nextStep2) {
            nextStep2.addEventListener('click', function() {
                if (validateStep(2)) {
                    currentStep = 3;
                    showStep(currentStep);
                }
            });
        }

        const nextStep3 = document.getElementById('nextStep3');
        if (nextStep3) {
            nextStep3.addEventListener('click', function() {
                if (validateStep(3)) {
                    currentStep = 4;
                    showStep(currentStep);
                }
            });
        }

        // Previous button handlers
        const prevStep2 = document.getElementById('prevStep2');
        if (prevStep2) {
            prevStep2.addEventListener('click', function() {
                currentStep = 1;
                showStep(currentStep);
            });
        }

        const prevStep3 = document.getElementById('prevStep3');
        if (prevStep3) {
            prevStep3.addEventListener('click', function() {
                currentStep = 2;
                showStep(currentStep);
            });
        }

        const prevStep4 = document.getElementById('prevStep4');
        if (prevStep4) {
            prevStep4.addEventListener('click', function() {
                currentStep = 3;
                showStep(currentStep);
            });
        }

        const nextStep4 = document.getElementById('nextStep4');
        if (nextStep4) {
            nextStep4.addEventListener('click', function() {
                if (validateStep(4)) {
                    // Show loading and submit the form
                    emailLoaderModal.style.display = 'flex';
                    nextStep4.disabled = true;
                    
                    // Submit the form to process registration
                    setTimeout(() => {
                        registerForm.submit();
                    }, 100);
                }
            });
        }

        const prevStep5 = document.getElementById('prevStep5');
        if (prevStep5) {
            prevStep5.addEventListener('click', function() {
                currentStep = 4;
                showStep(currentStep);
            });
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Use PHP step if it's 5 (verification step)
            if (phpStep === 5) {
                currentStep = 5;
                localStorage.removeItem('regData'); // Clear saved data
            } else {
                loadFromStorage();
            }
            showStep(currentStep);
            // Auto-save on input
            document.addEventListener('input', saveToStorage);
            document.addEventListener('change', saveToStorage);
        });

        // ID Upload functionality - Front
        const frontFileInput = document.getElementById('valid_id_front_image');
        const frontImagePreview = document.getElementById('frontImagePreview');
        const frontPreviewImg = document.getElementById('frontPreviewImg');
        const removeFrontImageBtn = document.getElementById('removeFrontImage');
        const frontCameraBtn = document.getElementById('frontCameraBtn');
        const frontCameraModal = new bootstrap.Modal(document.getElementById('frontCameraModal'));
        const frontCameraVideo = document.getElementById('frontCameraVideo');
        const frontCameraCanvas = document.getElementById('frontCameraCanvas');
        const frontCaptureBtn = document.getElementById('frontCaptureBtn');
        
        // ID Upload functionality - Back
        const backFileInput = document.getElementById('valid_id_back_image');
        const backImagePreview = document.getElementById('backImagePreview');
        const backPreviewImg = document.getElementById('backPreviewImg');
        const removeBackImageBtn = document.getElementById('removeBackImage');
        const backCameraBtn = document.getElementById('backCameraBtn');
        const backCameraModal = new bootstrap.Modal(document.getElementById('backCameraModal'));
        const backCameraVideo = document.getElementById('backCameraVideo');
        const backCameraCanvas = document.getElementById('backCameraCanvas');
        const backCaptureBtn = document.getElementById('backCaptureBtn');
        
        // Selfie Upload functionality
        const selfieInput = document.getElementById('selfie_with_id_image');
        const selfiePreview = document.getElementById('selfiePreview');
        const selfieImg = document.getElementById('selfieImg');
        const removeSelfieBtn = document.getElementById('removeSelfie');
        const selfieCameraBtn = document.getElementById('selfieCameraBtn');
        const selfieCameraModal = new bootstrap.Modal(document.getElementById('selfieCameraModal'));
        const selfieCameraVideo = document.getElementById('selfieCameraVideo');
        const selfieCameraCanvas = document.getElementById('selfieCameraCanvas');
        const selfieCaptureBtn = document.getElementById('selfieCaptureBtn');
        
        let frontStream = null;
        let backStream = null;
        let selfieStream = null;

        // Front ID file input change handler
        if (frontFileInput) {
            frontFileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        frontPreviewImg.src = e.target.result;
                        frontImagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Remove front image handler
        if (removeFrontImageBtn) {
            removeFrontImageBtn.addEventListener('click', function() {
                frontFileInput.value = '';
                frontImagePreview.style.display = 'none';
                frontPreviewImg.src = '';
            });
        }

        // Front camera button handler
        if (frontCameraBtn) {
            frontCameraBtn.addEventListener('click', function() {
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    navigator.mediaDevices.getUserMedia({ video: true })
                        .then(function(mediaStream) {
                            frontStream = mediaStream;
                            frontCameraVideo.srcObject = frontStream;
                            frontCameraModal.show();
                        })
                        .catch(function(error) {
                            alert('Camera access denied or not available');
                            console.error('Camera error:', error);
                        });
                } else {
                    alert('Camera not supported by this browser');
                }
            });
        }

        // Front capture button handler
        if (frontCaptureBtn) {
            frontCaptureBtn.addEventListener('click', function() {
                const context = frontCameraCanvas.getContext('2d');
                frontCameraCanvas.width = frontCameraVideo.videoWidth;
                frontCameraCanvas.height = frontCameraVideo.videoHeight;
                context.drawImage(frontCameraVideo, 0, 0);
                
                // Convert canvas to blob and create file
                frontCameraCanvas.toBlob(function(blob) {
                    const file = new File([blob], 'captured_id_front.jpg', { type: 'image/jpeg' });
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    frontFileInput.files = dt.files;
                    
                    // Show preview
                    frontPreviewImg.src = frontCameraCanvas.toDataURL();
                    frontImagePreview.style.display = 'block';
                    
                    // Stop camera and close modal
                    if (frontStream) {
                        frontStream.getTracks().forEach(track => track.stop());
                    }
                    frontCameraModal.hide();
                }, 'image/jpeg', 0.8);
            });
        }

        // Back ID file input change handler
        if (backFileInput) {
            backFileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        backPreviewImg.src = e.target.result;
                        backImagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Remove back image handler
        if (removeBackImageBtn) {
            removeBackImageBtn.addEventListener('click', function() {
                backFileInput.value = '';
                backImagePreview.style.display = 'none';
                backPreviewImg.src = '';
            });
        }

        // Back camera button handler
        if (backCameraBtn) {
            backCameraBtn.addEventListener('click', function() {
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    navigator.mediaDevices.getUserMedia({ video: true })
                        .then(function(mediaStream) {
                            backStream = mediaStream;
                            backCameraVideo.srcObject = backStream;
                            backCameraModal.show();
                        })
                        .catch(function(error) {
                            alert('Camera access denied or not available');
                            console.error('Camera error:', error);
                        });
                } else {
                    alert('Camera not supported by this browser');
                }
            });
        }

        // Back capture button handler
        if (backCaptureBtn) {
            backCaptureBtn.addEventListener('click', function() {
                const context = backCameraCanvas.getContext('2d');
                backCameraCanvas.width = backCameraVideo.videoWidth;
                backCameraCanvas.height = backCameraVideo.videoHeight;
                context.drawImage(backCameraVideo, 0, 0);
                
                // Convert canvas to blob and create file
                backCameraCanvas.toBlob(function(blob) {
                    const file = new File([blob], 'captured_id_back.jpg', { type: 'image/jpeg' });
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    backFileInput.files = dt.files;
                    
                    // Show preview
                    backPreviewImg.src = backCameraCanvas.toDataURL();
                    backImagePreview.style.display = 'block';
                    
                    // Stop camera and close modal
                    if (backStream) {
                        backStream.getTracks().forEach(track => track.stop());
                    }
                    backCameraModal.hide();
                }, 'image/jpeg', 0.8);
            });
        }

        // Stop front camera when modal is closed
        document.getElementById('frontCameraModal').addEventListener('hidden.bs.modal', function() {
            if (frontStream) {
                frontStream.getTracks().forEach(track => track.stop());
            }
        });

        // Stop back camera when modal is closed
        document.getElementById('backCameraModal').addEventListener('hidden.bs.modal', function() {
            if (backStream) {
                backStream.getTracks().forEach(track => track.stop());
            }
        });

        // Selfie input change handler
        if (selfieInput) {
            selfieInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        selfieImg.src = e.target.result;
                        selfiePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Remove selfie handler
        if (removeSelfieBtn) {
            removeSelfieBtn.addEventListener('click', function() {
                selfieInput.value = '';
                selfiePreview.style.display = 'none';
                selfieImg.src = '';
            });
        }

        // Selfie camera button handler
        if (selfieCameraBtn) {
            selfieCameraBtn.addEventListener('click', function() {
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
                        .then(function(mediaStream) {
                            selfieStream = mediaStream;
                            selfieCameraVideo.srcObject = selfieStream;
                            selfieCameraModal.show();
                        })
                        .catch(function(error) {
                            alert('Camera access denied or not available');
                            console.error('Camera error:', error);
                        });
                } else {
                    alert('Camera not supported by this browser');
                }
            });
        }

        // Selfie capture button handler
        if (selfieCaptureBtn) {
            selfieCaptureBtn.addEventListener('click', function() {
                const context = selfieCameraCanvas.getContext('2d');
                selfieCameraCanvas.width = selfieCameraVideo.videoWidth;
                selfieCameraCanvas.height = selfieCameraVideo.videoHeight;
                context.drawImage(selfieCameraVideo, 0, 0);
                
                // Convert canvas to blob and create file
                selfieCameraCanvas.toBlob(function(blob) {
                    const file = new File([blob], 'captured_selfie.jpg', { type: 'image/jpeg' });
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    selfieInput.files = dt.files;
                    
                    // Show preview
                    selfieImg.src = selfieCameraCanvas.toDataURL();
                    selfiePreview.style.display = 'block';
                    
                    // Stop camera and close modal
                    if (selfieStream) {
                        selfieStream.getTracks().forEach(track => track.stop());
                    }
                    selfieCameraModal.hide();
                }, 'image/jpeg', 0.8);
            });
        }

        // Stop selfie camera when modal is closed
        document.getElementById('selfieCameraModal').addEventListener('hidden.bs.modal', function() {
            if (selfieStream) {
                selfieStream.getTracks().forEach(track => track.stop());
            }
        });

        // District-Barangay dependency
        const districtSelect = document.getElementById('district_id');
        if (districtSelect) {
            districtSelect.addEventListener('change', function() {
                const districtId = this.value;
                const barangaySelect = document.getElementById('barangay_id');
                
                if (barangaySelect) {
                    // Clear existing options
                    barangaySelect.innerHTML = '<option value="">Loading...</option>';
                    
                    if (districtId) {
                        // Fetch barangays for selected district
                        fetch(`../api/get_barangays.php?district_id=${districtId}`)
                            .then(response => response.json())
                            .then(result => {
                                barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
                                const data = result.success ? result.data : result;
                                if (data && Array.isArray(data)) {
                                    data.forEach(barangay => {
                                        const option = document.createElement('option');
                                        option.value = barangay.id;
                                        option.textContent = barangay.name;
                                        if (barangay.alternate_name) {
                                            option.textContent += ` (${barangay.alternate_name})`;
                                        }
                                        barangaySelect.appendChild(option);
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching barangays:', error);
                                barangaySelect.innerHTML = '<option value="">Error loading barangays</option>';
                            });
                    } else {
                        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
                    }
                }
            });
        }
        // Get current location functionality
        const getCurrentLocationBtn = document.getElementById('getCurrentLocation');
        const currentAddressField = document.getElementById('current_address');
        const locationStatus = document.getElementById('locationStatus');
        
        if (getCurrentLocationBtn && currentAddressField) {
            getCurrentLocationBtn.addEventListener('click', function() {
                if (!navigator.geolocation) {
                    alert('Geolocation is not supported by this browser.');
                    return;
                }
                
                // Show loading status
                locationStatus.style.display = 'block';
                locationStatus.innerHTML = '<small class="text-info"><i class="bi bi-hourglass-split"></i> Getting your location...</small>';
                getCurrentLocationBtn.disabled = true;
                
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        
                        // Update status
                        locationStatus.innerHTML = '<small class="text-info"><i class="bi bi-search"></i> Finding address...</small>';
                        
                        // Use reverse geocoding to get address
                        reverseGeocode(lat, lng);
                    },
                    function(error) {
                        let errorMessage = 'Unable to get location. ';
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage += 'Location access denied by user.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage += 'Location information unavailable.';
                                break;
                            case error.TIMEOUT:
                                errorMessage += 'Location request timed out.';
                                break;
                            default:
                                errorMessage += 'Unknown error occurred.';
                                break;
                        }
                        
                        locationStatus.innerHTML = '<small class="text-danger"><i class="bi bi-exclamation-triangle"></i> ' + errorMessage + '</small>';
                        getCurrentLocationBtn.disabled = false;
                        
                        // Hide status after 5 seconds
                        setTimeout(() => {
                            locationStatus.style.display = 'none';
                        }, 5000);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 300000 // 5 minutes
                    }
                );
            });
        }
        
        function reverseGeocode(lat, lng) {
            // Using OpenStreetMap Nominatim API (free alternative to Google Maps)
            const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        // Format the address for Philippines
                        let formattedAddress = '';
                        const addr = data.address;
                        
                        if (addr) {
                            // Build address components
                            const components = [];
                            
                            // House number and street
                            if (addr.house_number) components.push(addr.house_number);
                            if (addr.road) components.push(addr.road);
                            
                            // Subdivision/Village
                            if (addr.village || addr.suburb || addr.neighbourhood) {
                                components.push(addr.village || addr.suburb || addr.neighbourhood);
                            }
                            
                            // Barangay
                            if (addr.city_district || addr.quarter) {
                                components.push(addr.city_district || addr.quarter);
                            }
                            
                            // City/Municipality
                            if (addr.city || addr.town || addr.municipality) {
                                components.push(addr.city || addr.town || addr.municipality);
                            }
                            
                            // Province
                            if (addr.state || addr.province) {
                                components.push(addr.state || addr.province);
                            }
                            
                            // Country
                            if (addr.country) {
                                components.push(addr.country);
                            }
                            
                            formattedAddress = components.join(', ');
                        }
                        
                        // Fallback to display_name if formatted address is empty
                        if (!formattedAddress) {
                            formattedAddress = data.display_name;
                        }
                        
                        currentAddressField.value = formattedAddress;
                        locationStatus.innerHTML = '<small class="text-success"><i class="bi bi-check-circle"></i> Location detected successfully!</small>';
                        
                        // Save to storage
                        saveToStorage();
                        
                        // Hide status after 3 seconds
                        setTimeout(() => {
                            locationStatus.style.display = 'none';
                        }, 3000);
                    } else {
                        throw new Error('No address found');
                    }
                })
                .catch(error => {
                    console.error('Geocoding error:', error);
                    locationStatus.innerHTML = '<small class="text-warning"><i class="bi bi-exclamation-triangle"></i> Could not determine address. Please enter manually.</small>';
                    
                    // Hide status after 5 seconds
                    setTimeout(() => {
                        locationStatus.style.display = 'none';
                    }, 5000);
                })
                .finally(() => {
                    getCurrentLocationBtn.disabled = false;
                });
        }
        
        // Enable/disable submit button based on terms acceptance
        document.addEventListener('DOMContentLoaded', function() {
            const termsCheckbox = document.getElementById('termsAccepted');
            const submitBtns = document.querySelectorAll('button[type="submit"]');
            
            if (termsCheckbox) {
                termsCheckbox.addEventListener('change', function() {
                    submitBtns.forEach(btn => {
                        btn.disabled = !this.checked;
                    });
                });
            }
        });
    </script>

    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>1. Acceptance of Terms</h6>
                    <p>By registering for LGU1 services, you agree to comply with these terms and conditions.</p>
                    
                    <h6>2. Account Registration</h6>
                    <p>You must provide accurate and complete information during registration. You are responsible for maintaining the confidentiality of your account credentials.</p>
                    
                    <h6>3. Use of Services</h6>
                    <p>LGU1 services are intended for legitimate government transactions and citizen services only. Misuse of the system is prohibited.</p>
                    
                    <h6>4. Data Accuracy</h6>
                    <p>You are responsible for ensuring all submitted information is accurate and up-to-date. False information may result in account suspension.</p>
                    
                    <h6>5. System Availability</h6>
                    <p>While we strive for continuous service, LGU1 reserves the right to perform maintenance and updates that may temporarily affect system availability.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Privacy Policy Modal -->
    <div class="modal fade" id="privacyModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Data Privacy Policy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>Data Privacy Act of 2012 Compliance</h6>
                    <p>Local Government Unit 1 is committed to protecting your personal data in accordance with the Data Privacy Act of 2012 (Republic Act No. 10173) and its implementing rules and regulations.</p>
                    
                    <h6>Data Collection</h6>
                    <p>We collect personal information necessary for providing government services, including:</p>
                    <ul>
                        <li>Personal identification information</li>
                        <li>Contact details</li>
                        <li>Address information</li>
                        <li>Valid ID images for verification</li>
                    </ul>
                    
                    <h6>Purpose of Data Processing</h6>
                    <p>Your personal data is processed for:</p>
                    <ul>
                        <li>Account verification and authentication</li>
                        <li>Delivery of government services</li>
                        <li>Communication regarding applications</li>
                        <li>Compliance with legal requirements</li>
                    </ul>
                    
                    <h6>Data Security</h6>
                    <p>We implement appropriate technical and organizational measures to ensure data security and protect against unauthorized access, alteration, disclosure, or destruction.</p>
                    
                    <h6>Your Rights</h6>
                    <p>Under the Data Privacy Act, you have the right to:</p>
                    <ul>
                        <li>Access your personal data</li>
                        <li>Request correction of inaccurate data</li>
                        <li>Object to processing in certain circumstances</li>
                        <li>Request deletion when legally permissible</li>
                    </ul>
                    
                    <h6>Data Retention</h6>
                    <p>Personal data will be retained only as long as necessary to fulfill collection purposes or as required by law.</p>
                    
                    <h6>Contact Information</h6>
                    <p>For data privacy concerns, contact our Data Protection Officer or the National Privacy Commission (NPC) at privacy@npc.gov.ph.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// SECURITY - Enforce HTTPS (commented out for local development)
// if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
//     $redirectURL = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
//     header("Location: $redirectURL");
//     exit();
// }

// SECURITY - Secure session configuration (commented out for local development)
// ini_set('session.cookie_httponly', 1);
// ini_set('session.cookie_secure', 1);
// ini_set('session.cookie_samesite', 'Strict');
// ini_set('session.use_strict_mode', 1);

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Create logs directory if it doesn't exist
if (!is_dir(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0755, true);
}

function sendLoginOTP($to, $name, $otp) {
    $mail = new PHPMailer(true);
    try {
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
        $mail->Timeout = 10;
        
        $mail->setFrom('lgu1.infrastructureutilities@gmail.com', 'LGU1 System');
        $mail->addAddress($to, $name);
        $mail->isHTML(true);
        $mail->Subject = 'LGU1 Login Verification Code';
        
        $mail->Body = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9;">
            <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h2 style="color: #00473e; text-align: center; margin-bottom: 20px;">LGU1 Login Verification</h2>
                <p style="color: #333; font-size: 16px;">Hello ' . htmlspecialchars($name) . ',</p>
                <p style="color: #666; line-height: 1.6;">Please use this verification code to complete your login:</p>
                <div style="text-align: center; margin: 30px 0;">
                    <div style="background: #faae2b; color: #00473e; font-size: 32px; font-weight: bold; padding: 15px 30px; border-radius: 8px; letter-spacing: 3px; display: inline-block;">' . $otp . '</div>
                    <p style="color: #fa5246; font-size: 12px; margin-top: 10px;">Valid for 10 minutes</p>
                </div>
                <p style="color: #666; font-size: 14px;">If you did not request this login, please ignore this email.</p>
                <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
                <p style="color: #999; font-size: 12px; text-align: center;">Local Government Unit 1 - Authentication System</p>
            </div>
        </div>';
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

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
$step = 1; // 1 = login form, 2 = OTP verification

// Check if user is in OTP verification step
if (isset($_SESSION['pending_login_user_id'])) {
    $step = 2;
}

// SECURITY - Rate limiting for login attempts (commented out for local development)
// $ip = $_SERVER['REMOTE_ADDR'] ?? '';
// $max_attempts = 5;
// $lockout_time = 900; // 15 minutes
// 
// // Check failed attempts
// $stmt = $conn->prepare('SELECT COUNT(*) as attempts, MAX(created_at) as last_attempt FROM audit_logs WHERE ip_address = ? AND action = "login_failed" AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)');
// $stmt->execute([$ip, $lockout_time]);
// $attempt_data = $stmt->fetch();
// 
// if ($attempt_data['attempts'] >= $max_attempts) {
//     $errors[] = 'Too many failed login attempts. Please try again later.';
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle OTP verification
    if (isset($_POST['verify_login_otp']) && isset($_SESSION['pending_login_user_id'])) {
        $otp = trim($_POST['otp']);
        $user_id = $_SESSION['pending_login_user_id'];
        
        // Validate OTP
        $stmt = $conn->prepare('SELECT * FROM user_otps WHERE user_id = ? AND otp_code = ? AND used = 0 AND expires_at > NOW() ORDER BY id DESC LIMIT 1');
        $stmt->execute([$user_id, $otp]);
        $otpRow = $stmt->fetch();
        
        if ($otpRow) {
            // Mark OTP as used
            $stmt = $conn->prepare('UPDATE user_otps SET used = 1 WHERE id = ?');
            $stmt->execute([$otpRow['id']]);
            
            // Get user data and complete login
            $stmt = $conn->prepare('
                SELECT u.*, 
                       r.name as role_name,
                       s.name as subsystem_name,
                       sr.role_name as subsystem_role_name
                FROM users u
                LEFT JOIN roles r ON u.role_id = r.id
                LEFT JOIN subsystems s ON u.subsystem_id = s.id
                LEFT JOIN subsystem_roles sr ON u.subsystem_role_id = sr.id
                WHERE u.id = ?
            ');
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Complete login process (same as before)
                session_unset();
                
                $stmt = $conn->prepare('UPDATE users SET last_login = NOW() WHERE id = ?');
                $stmt->execute([$user['id']]);
                
                $stmt = $conn->prepare('INSERT INTO audit_logs (user_id, action, ip_address, user_agent) VALUES (?, ?, ?, ?)');
                $stmt->execute([
                    $user['id'], 
                    'login_success', 
                    $_SERVER['REMOTE_ADDR'] ?? '', 
                    $_SERVER['HTTP_USER_AGENT'] ?? ''
                ]);
                
                $user_roles = [];
                if ($user['role_name']) {
                    $user_roles[] = $user['role_name'];
                }
                if ($user['subsystem_role_name']) {
                    $user_roles[] = $user['subsystem_role_name'];
                }
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['mobile_number'] = $user['mobile_number'];
                $_SESSION['roles'] = $user_roles;
                
                if ($user['subsystem_name'] === 'Housing and Resettlement Management') {
                    $_SESSION['user_role'] = $user['subsystem_role_name'] ?? 'Housing Staff';
                    $_SESSION['user_name'] = $user['full_name'];
                    $_SESSION['full_name'] = $user['full_name'];
                } else {
                    $_SESSION['user_role'] = $user_roles[0] ?? 'citizen';
                    $_SESSION['user_name'] = $user['full_name'];
                    $_SESSION['full_name'] = $user['full_name'];
                }
                
                $_SESSION['subsystems'] = $user['subsystem_name'] ? [$user['subsystem_name']] : [];
                $_SESSION['subsystem_roles'] = $user['subsystem_role_name'] ? [$user['subsystem_role_name']] : [];
                $_SESSION['login_time'] = time();
                $_SESSION['last_activity'] = time();
                
                $session_token = bin2hex(random_bytes(32));
                $_SESSION['session_token'] = $session_token;
                
                $stmt = $conn->prepare('INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, created_at, last_activity) VALUES (?, ?, ?, ?, NOW(), NOW())');
                $stmt->execute([
                    $user['id'],
                    $session_token,
                    $_SERVER['REMOTE_ADDR'] ?? '',
                    $_SERVER['HTTP_USER_AGENT'] ?? ''
                ]);
                
                // Redirect logic (same as before)
                if (in_array('super admin', $user_roles)) {
                    $redirect_url = '../SuperAdmin/dashboard.php';
                } elseif (in_array('citizen', $user_roles)) {
                    $redirect_url = '../Citizen/home.php';
                } elseif ($user['subsystem_name']) {
                    $params = '?user_id=' . $user['id'] . '&token=' . base64_encode($user['username']);
                    switch ($user['subsystem_name']) {
                        case 'Infrastructure Project Management':
                            $redirect_url = 'https://pm.local-government-unit-1-ph.com/dashboard.php' . $params;
                            break;
                        case 'Utility Billing and Monitoring Management (Water, Electricity)':
                            $redirect_url = 'https://staff-billing.local-government-unit-1-ph.com/staff/dashboard.php' . $params;
                            break;
                        case 'Road and Transportation Infrastructure Monitoring':
                            if ($user['subsystem_role_name'] === 'Admin') {
                                $redirect_url = 'https://road-trans.local-government-unit-1-ph.com/admin/dashboard.php' . $params;
                            } elseif ($user['subsystem_role_name'] === 'Inspector') {
                                $redirect_url = 'https://road-trans.local-government-unit-1-ph.com/inspector/dashboard.php' . $params;
                            } else {
                                $redirect_url = 'https://resident-road.local-government-unit-1-ph.com/dashboard.php' . $params;
                            }
                            break;
                        case 'Public Facilities Reservation System':
                            // Prepare SSO parameters
    $params = [
        'user_id' => $user['id'],
        'username' => $user['username'],
        'role' => 'staff',
        'subsystem' => 'Public Facilities Reservation System',
        'subsystem_role_name' => $user['subsystem_role_name'],
        'sig' => $signature,
        'ts' => time()
    ];
    
    $paramString = http_build_query($params);
    
    // Redirect based on role
    if ($user['subsystem_role_name'] === 'Admin') {
        $redirect_url = 'https://facilities.local-government-unit-1-ph.com/admin/dashboard?' . $paramString;
    } elseif ($user['subsystem_role_name'] === 'Staff') {
        $redirect_url = 'https://facilities.local-government-unit-1-ph.com/staff/dashboard?' . $paramString;
    } else {
        $redirect_url = 'https://facilities.local-government-unit-1-ph.com/citizen/dashboard?' . $paramString;
    }
    
    // Execute redirect
    header("Location: " . $redirect_url);
    exit();
    
    break;
                        case 'Community Infrastructure Maintenance Management':
                            $redirect_url = 'https://community.local-government-unit-1-ph.com/dashboard.php' . $params;
                            break;
                             if ($user['subsystem_role_name'] === 'Admin') {
                                $redirect_url = 'https://community.local-government-unit-1-ph.com/admin/dashboard.php' . $params;
                            } elseif ($user['subsystem_role_name'] === 'Planning Officer') {
                                $redirect_url = 'https://community.local-government-unit-1-ph.com/officer/dashboard.php' . $params;
                            } elseif ($user['subsystem_role_name'] === 'GIS Staff') {
                                $redirect_url = 'https://community.local-government-unit-1-ph.com/staff/dashboard.php' . $params;
                            } else {
                                $redirect_url = 'https://community.local-government-unit-1-ph.com/resident/dashboard.php' . $params;
                            }
                            break;
                        case 'Urban Planning and Development':
                            if ($user['subsystem_role_name'] === 'Admin') {
                                $redirect_url = 'https://planning.local-government-unit-1-ph.com/admin/dashboard.php' . $params;
                            } elseif ($user['subsystem_role_name'] === 'Planning Officer') {
                                $redirect_url = 'https://planning.local-government-unit-1-ph.com/officer/dashboard.php' . $params;
                            } elseif ($user['subsystem_role_name'] === 'GIS Staff') {
                                $redirect_url = 'https://planning.local-government-unit-1-ph.com/staff/dashboard.php' . $params;
                            } else {
                                $redirect_url = 'https://planning.local-government-unit-1-ph.com/powner/dashboard.php' . $params;
                            }
                            break;
                        case 'Land Registration and Titling System':
                            $redirect_url = 'https://staff-lrts.local-government-unit-1-ph.com/dashboard.php' . $params;
                            break;
                        case 'Housing and Resettlement Management':
                            if ($user['subsystem_role_name'] === 'Admin') {
                                $redirect_url = 'https://qcitizen-homes.local-government-unit-1-ph.com/admin/dashboard.php' . $params;
                            } elseif ($user['subsystem_role_name'] === 'Staff') {
                                $redirect_url = 'https://qcitizen-homes.local-government-unit-1-ph.com/staff/dashboard.php' . $params;
                            } else {
                                $redirect_url = 'https://applicant-housing.local-government-unit-1-ph.com/dashboard.php' . $params;
                            }
                            break;
                        case 'Renewable Energy Project Management':
                            $redirect_url = 'https://renew-energy.local-government-unit-1-ph.com/dashboard.php' . $params;
                            break;
                        case 'Energy Efficiency and Conservative Management':
                            if ($user['subsystem_role_name'] === 'Residents') {
                                $redirect_url = 'https://applicant-housing.local-government-unit-1-ph.com/resident-dashboard.php' . $params;
                            } else {
                                $redirect_url = 'https://energy.local-government-unit-1-ph.com/admin-dashboard1.php' . $params;
                            }
                            break;
                        default:
                            $redirect_url = 'dashboard.php';
                    }
                } else {
                    $redirect_url = 'dashboard.php';
                }
                
                header('Location: ' . $redirect_url);
                exit;
            }
        } else {
            $errors[] = 'Invalid or expired OTP. Please try again.';
            $step = 2;
        }
    }
    // Handle initial login
    else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
    
    if (!$username || !$password) {
        $errors[] = 'Username and password are required.';
    } else {
        // Get user with roles and subsystems - allow super admin without email verification
        $stmt = $conn->prepare('
            SELECT u.*, 
                   r.name as role_name,
                   s.name as subsystem_name,
                   sr.role_name as subsystem_role_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            LEFT JOIN subsystems s ON u.subsystem_id = s.id
            LEFT JOIN subsystem_roles sr ON u.subsystem_role_id = sr.id
            WHERE (u.username = ? OR u.email = ?) AND u.status = "active"
        ');
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        // SECURITY - Added empty($errors) check for rate limiting
        if ($user && password_verify($password, $user['password_hash']) /* && empty($errors) */) {
            // Check if user can login (email verified OR is admin)
            $can_login = ($user['is_email_verified'] == 1) || 
                        ($user['role_name'] && in_array($user['role_name'], ['super admin', 'system admin']));
            
            if (!$can_login) {
                $errors[] = 'Please verify your email before logging in.';
            } else {
                // Generate and send OTP for login verification
                $otp = rand(100000, 999999);
                $now = date('Y-m-d H:i:s');
                
                // Store OTP
                $stmt = $conn->prepare('INSERT INTO user_otps (user_id, otp_code, expires_at, used, created_at) VALUES (?, ?, ?, 0, ?)');
                $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                $stmt->execute([$user['id'], (string)$otp, $expires, $now]);
                
                // Send OTP email
                try {
                    sendLoginOTP($user['email'], $user['full_name'], $otp);
                    $_SESSION['pending_login_user_id'] = $user['id'];
                    $step = 2;
                    $success_message = 'OTP sent to your email. Please check your inbox.';
                } catch (Exception $e) {
                    $_SESSION['pending_login_user_id'] = $user['id'];
                    $step = 2;
                    $success_message = 'OTP is being sent. Please check your email.';
                }
            }
        } else {
            $errors[] = 'Invalid username/email or password.';
        }
    }
    
    if (!empty($errors) && $step == 1) {
        $stmt = $conn->prepare('INSERT INTO audit_logs (user_id, action, ip_address, user_agent) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            null, 
            'login_failed', 
            $_SERVER['REMOTE_ADDR'] ?? '', 
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LGU1 - Login</title>
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
        .loader-overlay {
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
        .loader-content {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            text-align: center;
            min-width: 300px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        .loader-spinner {
            display: inline-block;
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--headline);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 1rem;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .login-logo {
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
        .register-link {
            color: var(--headline);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }
        .register-link:hover {
            color: var(--tertiary);
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
                        <span>Authentication Portal</span>
                    </h1>
                    <p class="mb-4 hero-description">
                        Secure access to LGU1 services and systems. Your gateway to efficient government services and digital infrastructure management.
                    </p>
                </div>

                <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
                    <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                    <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

                    <div class="card bg-glass">
                        <div class="card-body px-4 py-5 px-md-5">
                            <div class="text-center mb-4">
                                <img src="assets/images/logo.png" alt="LGU1 Logo" style="max-width: 80px; margin-bottom: 1rem;">
                                <h2 style="color: var(--headline); font-family: 'Merriweather', serif; font-weight: 700;">Sign In</h2>
                                <p class="text-muted"><i class="bi bi-shield-check-fill"></i> Secure Login</p>
                            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success_message) ?>
                </div>
            <?php endif; ?>

            <?php if ($step == 1): ?>
            <!-- Login Form -->
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label"><i class="bi bi-person-badge"></i> Username or Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" 
                               class="form-control" 
                               id="username" 
                               name="username" 
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                               required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label"><i class="bi bi-lock"></i> Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               autocomplete="current-password"
                               required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye" id="passwordIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100" id="loginBtn">
                    <i class="bi bi-box-arrow-in-right"></i> Sign In
                </button>
            </form>
            <?php else: ?>
            <!-- OTP Verification Form -->
            <div class="text-center mb-4">
                <i class="bi bi-shield-check" style="font-size: 3rem; color: var(--highlight);"></i>
                <h4 style="color: var(--headline); margin-top: 1rem;">Verify Your Identity</h4>
                <p class="text-muted">Enter the 6-digit code sent to your email</p>
            </div>
            
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="otp" class="form-label"><i class="bi bi-key"></i> Verification Code</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                        <input type="text" 
                               class="form-control text-center" 
                               id="otp" 
                               name="otp" 
                               placeholder="000000"
                               maxlength="6"
                               pattern="[0-9]{6}"
                               style="font-size: 1.5rem; letter-spacing: 0.5rem;"
                               required>
                    </div>
                    <small class="text-muted">Code expires in 10 minutes</small>
                </div>

                <button type="submit" name="verify_login_otp" class="btn btn-primary w-100">
                    <i class="bi bi-check-circle"></i> Verify & Sign In
                </button>
            </form>
            <?php endif; ?>

            <div class="divider">or</div>
            <div class="text-center">
                <a href="register.php" class="register-link"><i class="bi bi-person-plus"></i> Create New Account</a>
            </div>
            <div class="text-center mt-2">
                <a href="forgot_password.php" class="register-link"><i class="bi bi-key"></i> Forgot Password?</a>
            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Loader Overlay -->
    <div class="loader-overlay" id="loaderOverlay">
        <div class="loader-content">
            <div class="loader-spinner"></div>
            <h5 style="color: var(--headline); margin-bottom: 0.5rem;">Sending Verification Code</h5>
            <p class="text-muted mb-0">Please wait while we send the OTP to your email...</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Password toggle functionality
        const togglePassword = document.getElementById('togglePassword');
        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
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
        }
        
        // Show loader when submitting login form (step 1 only)
        const loginBtn = document.getElementById('loginBtn');
        if (loginBtn && <?= $step ?> === 1) {
            loginBtn.closest('form').addEventListener('submit', function() {
                document.getElementById('loaderOverlay').style.display = 'flex';
                loginBtn.disabled = true;
                loginBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Sending OTP...';
            });
        }


    </script>
</body>
</html>
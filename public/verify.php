<?php
// SECURITY - Enforce HTTPS (commented out for development)
// if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
//     $redirectURL = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
//     header("Location: $redirectURL");
//     exit();
// }

// SECURITY - Secure session configuration (commented out for development)
// ini_set('session.cookie_httponly', 1);
// ini_set('session.cookie_secure', 1);
// ini_set('session.cookie_samesite', 'Strict');
// ini_set('session.use_strict_mode', 1);

session_start();
require_once __DIR__ . '/../config/config.php';

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

$step = 1;
$error = '';
$success = '';
$token = $_GET['token'] ?? '';
$user = null;

// SECURITY - Rate limiting for verification attempts (commented out for development)
// $ip = $_SERVER['REMOTE_ADDR'] ?? '';
// $max_attempts = 5;
// $lockout_time = 900; // 15 minutes
// 
// $stmt = $conn->prepare('SELECT COUNT(*) as attempts FROM audit_logs WHERE ip_address = ? AND action = "verification_failed" AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)');
// $stmt->execute([$ip, $lockout_time]);
// $attempt_data = $stmt->fetch();
// 
// if ($attempt_data['attempts'] >= $max_attempts) {
//     $error = 'Too many verification attempts. Please try again later.';
// }

if ($token) {
    // SECURITY - Validate token format (commented out for development)
    // if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
    //     $error = 'Invalid verification link format.';
    // } else {
        $stmt = $conn->prepare('SELECT * FROM users WHERE email_verification_token = ?');
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        if (!$user) {
            // SECURITY - Log failed verification attempt (commented out for development)
            // $stmt = $conn->prepare('INSERT INTO audit_logs (user_id, action, ip_address, user_agent) VALUES (?, ?, ?, ?)');
            // $stmt->execute([null, 'verification_failed', $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']);
            $error = 'Invalid or expired verification link.';
        } elseif ($user['is_email_verified']) {
            $error = 'Your email is already verified.';
    } else {
        $step = 2;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp'])) {
            $otp = trim($_POST['otp']);
            $stmt = $conn->prepare('SELECT * FROM user_otps WHERE user_id = ? AND otp_code = ? AND used = 0 AND expires_at > NOW() ORDER BY id DESC LIMIT 1');
            $stmt->execute([$user['id'], $otp]);
            $otpRow = $stmt->fetch();
            if ($otpRow) {
                // Mark OTP as used
                $stmt = $conn->prepare('UPDATE user_otps SET used = 1 WHERE id = ?');
                $stmt->execute([$otpRow['id']]);
                // Mark user as verified
                $stmt = $conn->prepare('UPDATE users SET is_email_verified = 1, email_verified_at = NOW(), status = "active" WHERE id = ?');
                $stmt->execute([$user['id']]);
                
                // SECURITY - Log successful verification (commented out for development)
                // $stmt = $conn->prepare('INSERT INTO audit_logs (user_id, action, ip_address, user_agent) VALUES (?, ?, ?, ?)');
                // $stmt->execute([$user['id'], 'email_verified', $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']);
                
                // SECURITY - Regenerate session ID (commented out for development)
                // session_regenerate_id(true);
                
                $success = 'Your email has been verified! You may now log in.';
                $step = 3;
            } else {
                // SECURITY - Log failed OTP attempt (commented out for development)
                // $stmt = $conn->prepare('INSERT INTO audit_logs (user_id, action, ip_address, user_agent) VALUES (?, ?, ?, ?)');
                // $stmt->execute([$user['id'], 'verification_failed', $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']);
                $error = 'Invalid or expired OTP.';
            }
        }
    }
    // }
} else {
    $error = 'No verification token provided.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - LGU1</title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
        .verify-logo {
            display: block;
            margin: 0 auto 1.5rem auto;
            max-width: 90px;
            filter: drop-shadow(0 2px 8px #00473e22);
        }
        .verify-title {
            color: <?= $colors['headline'] ?>;
            font-family: 'Merriweather', serif;
            font-weight: 700;
            text-align: center;
            margin-bottom: 1.2rem;
        }
        .form-label {
            color: <?= $colors['headline'] ?>;
            font-weight: 600;
        }
        .btn-primary {
            background: linear-gradient(90deg, <?= $colors['button'] ?> 60%, <?= $colors['highlight'] ?> 100%);
            color: <?= $colors['button_text'] ?>;
            border: none;
            font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px #faae2b33;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .btn-primary:hover, .btn-primary:focus {
            background: linear-gradient(90deg, <?= $colors['highlight'] ?> 60%, <?= $colors['button'] ?> 100%);
            color: <?= $colors['button_text'] ?>;
            box-shadow: 0 4px 16px #faae2b44;
        }
        .alert {
            font-size: 1rem;
            margin-bottom: 1.5rem;
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
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.min.css" rel="stylesheet">
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <section class="background-radial-gradient overflow-hidden">
        <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
            <div class="row gx-lg-5 align-items-center mb-5">
                <div class="col-lg-6 mb-5 mb-lg-0" style="z-index: 10">
                    <h1 class="my-5 display-5 fw-bold ls-tight hero-text">
                        Local Government Unit 1 <br />
                        <span>Email Verification</span>
                    </h1>
                    <p class="mb-4 hero-description">
                        Verify your email address to activate your LGU1 account and gain access to government services.
                    </p>
                </div>

                <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
                    <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                    <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

                    <div class="card bg-glass">
                        <div class="card-body px-4 py-5 px-md-5">
                            <div class="text-center mb-4">
                                <img src="assets/images/logo.png" alt="LGU1 Logo" style="max-width: 80px; margin-bottom: 1rem;">
                                <h2 style="color: var(--headline); font-family: 'Merriweather', serif; font-weight: 700;">Email Verification</h2>
                                <p class="text-muted"><i class="bi bi-shield-check"></i> Account Activation</p>
                            </div>
            <?php if ($error): ?>
                <script>
                window.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Verification Error',
                        html: '<?= addslashes($error) ?>',
                        confirmButtonColor: '<?= $colors['button'] ?>',
                        background: 'rgba(255,255,255,0.98)',
                        backdrop: 'rgba(0,0,0,0.2)',
                        showClass: { popup: 'animate__animated animate__shakeX' }
                    });
                });
                </script>
            <?php elseif ($step === 2): ?>
                <script>
                window.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'info',
                        title: 'Enter OTP',
                        html: 'Please enter the OTP sent to your email to verify your account.',
                        confirmButtonColor: '<?= $colors['button'] ?>',
                        background: 'rgba(255,255,255,0.98)',
                        backdrop: 'rgba(0,0,0,0.2)',
                        showClass: { popup: 'animate__animated animate__fadeInDown' }
                    });
                });
                </script>
                <form method="post" autocomplete="off">
                    <div class="mb-3">
                        <label for="otp" class="form-label">One-Time Password (OTP)</label>
                        <input type="text" class="form-control" id="otp" name="otp" required maxlength="6" pattern="[0-9]{6}" placeholder="Enter 6-digit OTP">
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-shield-check"></i> Verify</button>
                    <!-- SECURITY - CSRF protection token (commented out for development) -->
                    <!-- <input type="hidden" name="csrf_token" value="<?= bin2hex(random_bytes(32)) ?>"> -->
                </form>
            <?php elseif ($success): ?>
                <script>
                window.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Verified!',
                        html: '<?= addslashes($success) ?>',
                        confirmButtonColor: '<?= $colors['button'] ?>',
                        background: 'rgba(255,255,255,0.98)',
                        backdrop: 'rgba(0,0,0,0.2)',
                        showClass: { popup: 'animate__animated animate__fadeInUp' }
                    });
                });
                </script>
                <div class="text-center mt-3">
                    <a href="login.php" class="btn btn-success"><i class="bi bi-box-arrow-in-right"></i> Go to Login</a>
                </div>
            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>

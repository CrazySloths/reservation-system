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
    require_once __DIR__ . '/../vendor/autoload.php';

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
    $step = 1;

    // SECURITY - Rate limiting for password reset attempts (commented out for development)
    // $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    // $max_attempts = 3;
    // $lockout_time = 1800; // 30 minutes
    // 
    // $stmt = $conn->prepare('SELECT COUNT(*) as attempts FROM audit_logs WHERE ip_address = ? AND action = "password_reset_failed" AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)');
    // $stmt->execute([$ip, $lockout_time]);
    // $attempt_data = $stmt->fetch();
    // 
    // if ($attempt_data['attempts'] >= $max_attempts) {
    //     $errors[] = 'Too many password reset attempts. Please try again later.';
    // }

    function sendPasswordResetEmail($to, $name, $otp) {
        global $colors;
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'lgu1.infrastructureutilities@gmail.com';
            $mail->Password = 'kpyv rwvp tmxw zvoq';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('lgu1.infrastructureutilities@gmail.com', 'LGU1 Password Reset');
            $mail->addAddress($to, $name);
            $mail->isHTML(true);
            $mail->Subject = '🔐 LGU1 Password Reset - Verification Code';
            
            $mail->Body = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9;">
                <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h2 style="color: #00473e; text-align: center; margin-bottom: 20px;">LGU1 Password Reset</h2>
                    <p style="color: #333; font-size: 16px;">Hello ' . htmlspecialchars($name) . ',</p>
                    <p style="color: #666; line-height: 1.6;">Please use this verification code to reset your password:</p>
                    <div style="text-align: center; margin: 30px 0;">
                        <div style="background: #faae2b; color: #00473e; font-size: 32px; font-weight: bold; padding: 15px 30px; border-radius: 8px; letter-spacing: 3px; display: inline-block;">' . $otp . '</div>
                        <p style="color: #fa5246; font-size: 12px; margin-top: 10px;">Valid for 10 minutes</p>
                    </div>
                    <p style="color: #666; font-size: 14px;">If you did not request this, please ignore this email.</p>
                    <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
                    <p style="color: #999; font-size: 12px; text-align: center;">Local Government Unit 1 - Authentication System</p>
                </div>
            </div>';
            
            $mail->AltBody = "LGU1 Password Reset\n\nHello $name,\n\nYour password reset code: $otp\n(Valid for 10 minutes)\n\nIf you did not request this, please ignore this email.\n\n---\nLocal Government Unit 1";
            
            $mail->send();
            error_log('Email sent successfully to: ' . $to);
            echo '<script>console.log("Email sent successfully to: ' . addslashes($to) . '");</script>';
            return true;
        } catch (Exception $e) {
            error_log('Email sending failed: ' . $e->getMessage());
            echo '<script>console.error("Email Error: ' . addslashes($e->getMessage()) . '");</script>';
            return $e->getMessage();
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        error_log('POST received: ' . print_r($_POST, true));
        echo '<script>console.log("POST data:", ' . json_encode($_POST) . ');</script>';
        
        echo '<script>console.log("Checking for send_otp:", ' . (isset($_POST['send_otp']) ? 'true' : 'false') . ');</script>';
        
        if (isset($_POST['send_otp'])) {
            echo '<script>console.log("Send OTP button clicked");</script>';
            // Step 1: Send OTP
            $email = trim($_POST['email'] ?? '');
            
            echo '<script>console.log("Email input:", "' . addslashes($email) . '");</script>';
            
            if (!$email) {
                $errors[] = 'Email address is required.';
                echo '<script>console.log("Error: Email required");</script>';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Valid email address is required.';
                echo '<script>console.log("Error: Invalid email format");</script>';
            } else {
                echo '<script>console.log("Email validation passed");</script>';
                // Check if user exists
                $stmt = $conn->prepare('SELECT id, full_name, email FROM users WHERE email = ? AND status = "active"');
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if ($user) {
                    $otp = rand(100000, 999999);
                    $now = date('Y-m-d H:i:s');
                    $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                    
                    // Mark previous OTPs as used
                    $stmt = $conn->prepare('UPDATE user_otps SET used = 1 WHERE user_id = ? AND used = 0');
                    $stmt->execute([$user['id']]);
                    
                    // Store new OTP
                    $stmt = $conn->prepare('INSERT INTO user_otps (user_id, otp_code, expires_at, used, created_at) VALUES (?, ?, ?, 0, ?)');
                    $stmt->execute([$user['id'], (string)$otp, $expires, $now]);
                    
                    // Send email
                    $mailResult = sendPasswordResetEmail($email, $user['full_name'], $otp);
                    // Send email
                    echo '<script>console.log("Attempting to send email to: ' . addslashes($email) . '");</script>';
                    error_log('Attempting to send email to: ' . $email);
                    
                    $mailResult = sendPasswordResetEmail($email, $user['full_name'], $otp);
                    error_log('Mail result: ' . ($mailResult === true ? 'SUCCESS' : $mailResult));
                    echo '<script>console.log("Mail result: ' . ($mailResult === true ? 'SUCCESS' : addslashes($mailResult)) . '");</script>';
                    
                    if ($mailResult === true) {
                        $_SESSION['reset_user_id'] = $user['id'];
                        $_SESSION['reset_email'] = $email;
                        $step = 2;
                        $success_message = 'Verification code sent to your email!';
                        echo '<script>console.log("Email sent successfully, moving to step 2");</script>';
                    } else {
                        $_SESSION['reset_user_id'] = $user['id'];
                        $_SESSION['reset_email'] = $email;
                        $_SESSION['debug_otp'] = $otp;
                        $step = 2;
                        $success_message = 'Debug: Your reset code is ' . $otp . ' (Email error: ' . $mailResult . ')';
                        echo '<script>console.error("Email failed, showing debug OTP: ' . $otp . '");</script>';
                    }
                } else {
                    // SECURITY - Generic error message (reverted for development)
                    $errors[] = 'No account found with this email address.';
                }
            }
        } elseif ((isset($_POST['verify_otp']) || (isset($_POST['otp']) && !empty($_POST['otp']))) && isset($_SESSION['reset_user_id'])) {
            // Step 2: Verify OTP
            $otp = trim($_POST['otp']);
            $user_id = $_SESSION['reset_user_id'];
            
            // Get the latest unused OTP for this user
            $stmt = $conn->prepare('SELECT * FROM user_otps WHERE user_id = ? AND used = 0 ORDER BY id DESC LIMIT 1');
            $stmt->execute([$user_id]);
            $otpRow = $stmt->fetch();
            
            // Check if OTP matches and is not expired
            if ($otpRow && $otpRow['otp_code'] == $otp) {
                $currentTime = new DateTime();
                $expiryTime = new DateTime($otpRow['expires_at']);
                
                if ($currentTime <= $expiryTime) {
                    // OTP is valid
                } else {
                    $otpRow = null; // Expired
                    $errors[] = 'OTP has expired. Please request a new one.';
                }
            } else {
                $otpRow = null; // Invalid OTP
            }
            
            if ($otpRow) {
                // Mark OTP as used
                $stmt = $conn->prepare('UPDATE user_otps SET used = 1 WHERE id = ?');
                $stmt->execute([$otpRow['id']]);
                
                $_SESSION['otp_verified'] = true;
                $step = 3;
            } else {
                $errors[] = 'Invalid or expired OTP code.';
            }
        } elseif (isset($_POST['reset_password']) && isset($_SESSION['otp_verified'])) {
            // Step 3: Reset Password
            echo '<script>console.log("Reset password triggered");</script>';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            echo '<script>console.log("Password fields:", "' . (empty($password) ? 'empty' : 'filled') . '", "' . (empty($confirm_password) ? 'empty' : 'filled') . '");</script>';
            
            if (!$password || !$confirm_password) {
                $errors[] = 'Both password fields are required.';
            } elseif ($password !== $confirm_password) {
                $errors[] = 'Passwords do not match.';
            // SECURITY - Enhanced password validation (reverted for development)
            } elseif (strlen($password) < 6) {
                $errors[] = 'Password must be at least 6 characters.';
            // } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
            //     $errors[] = 'Password must contain uppercase, lowercase, and number.';
            } else {
                $user_id = $_SESSION['reset_user_id'];
                // SECURITY - Use stronger password hashing (reverted for development)
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                // if (defined('PASSWORD_ARGON2ID')) {
                //     $password_hash = password_hash($password, PASSWORD_ARGON2ID, [
                //         'memory_cost' => 65536,
                //         'time_cost' => 4,
                //         'threads' => 3
                //     ]);
                // }
                
                $stmt = $conn->prepare('UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?');
                $stmt->execute([$password_hash, $user_id]);
                
                // Clear session
                unset($_SESSION['reset_user_id']);
                unset($_SESSION['reset_email']);
                unset($_SESSION['otp_verified']);
                
                // SECURITY - Regenerate session ID for security (commented out for development)
                // session_regenerate_id(true);
                
                $step = 4;
                $success = true;
                $success_message = 'Password reset successfully!';
            }
        } elseif (isset($_POST['resend_otp']) && isset($_SESSION['reset_user_id'])) {
            // Resend OTP
            $user_id = $_SESSION['reset_user_id'];
            $email = $_SESSION['reset_email'];
            
            $stmt = $conn->prepare('SELECT full_name FROM users WHERE id = ?');
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            if ($user) {
                $otp = rand(100000, 999999);
                $now = date('Y-m-d H:i:s');
                
                // Mark previous OTPs as used
                $stmt = $conn->prepare('UPDATE user_otps SET used = 1 WHERE user_id = ? AND used = 0');
                $stmt->execute([$user_id]);
                
                // Store new OTP
                $stmt = $conn->prepare('INSERT INTO user_otps (user_id, otp_code, expires_at, used, created_at) VALUES (?, ?, ?, 0, ?)');
                $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                $stmt->execute([$user_id, (string)$otp, $expires, $now]);
                
                // Send email with detailed logging
                echo '<script>console.log("Resending OTP to: ' . addslashes($email) . '");</script>';
                error_log('Resending OTP to: ' . $email . ' with code: ' . $otp);
                
                $mailResult = sendPasswordResetEmail($email, $user['full_name'], $otp);
                
                if ($mailResult === true) {
                    $success_message = 'OTP resent successfully!';
                    echo '<script>console.log("OTP resent successfully");</script>';
                } else {
                    error_log('Resend email error: ' . $mailResult);
                    $_SESSION['debug_otp'] = $otp;
                    $success_message = 'Email failed. Your reset code is: ' . $otp;
                    echo '<script>console.error("Resend Email Error: ' . addslashes($mailResult) . '");</script>';
                }
                $step = 2;
            }
        }
    }

    // Determine current step based on session state
    if (isset($_SESSION['otp_verified'])) {
        $step = 3;
    } elseif (isset($_SESSION['reset_user_id'])) {
        $step = 2;
    } else {
        $step = 1;
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Forgot Password - LGU1</title>
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
            .reset-container {
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
            .reset-container::before {
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
            .step { display: none; }
            .step.active { display: block; }
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
                            <span>Password Recovery</span>
                        </h1>
                        <p class="mb-4 hero-description">
                            Secure password reset for LGU1 accounts. Recover access to your government services and digital infrastructure.
                        </p>
                    </div>

                    <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
                        <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                        <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

                        <div class="card bg-glass">
                            <div class="card-body px-4 py-5 px-md-5">
                                <div class="text-center mb-4">
                                    <img src="assets/images/logo.png" alt="LGU1 Logo" style="max-width: 80px; margin-bottom: 1rem;">
                                    <h2 style="color: var(--headline); font-family: 'Merriweather', serif; font-weight: 700;">Reset Password</h2>
                                    <p class="text-muted"><i class="bi bi-key-fill"></i> Password Recovery</p>
                                </div>
                
                <?php if (!empty($errors)): ?>
                    <script>
                        window.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                html: '<?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>',
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
                                title: 'Success',
                                text: '<?= htmlspecialchars($success_message) ?>',
                                confirmButtonColor: '<?= $colors['button'] ?>',
                            });
                        });
                    </script>
                <?php endif; ?>

                <form method="post">
                    <!-- Step 1: Enter Email -->
                    <div class="step <?= $step === 1 ? 'active' : '' ?>">
                        <p class="text-muted text-center mb-4">Enter your email address to receive a verification code</p>
                        <div class="mb-3">
                            <label for="email" class="form-label"><i class="bi bi-envelope"></i> Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>
                        </div>
                        <button type="submit" name="send_otp" value="1" class="btn btn-primary w-100">
                            <i class="bi bi-send"></i> Send Verification Code
                        </button>
                    </div>

                    <!-- Step 2: Verify OTP -->
                    <div class="step <?= $step === 2 ? 'active' : '' ?>">
                        <p class="text-muted text-center mb-4">Enter the 6-digit code sent to your email</p>
                        <div class="mb-3">
                            <label for="otp" class="form-label"><i class="bi bi-shield-check"></i> Verification Code</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-123"></i></span>
                                <input type="text" class="form-control" id="otp" name="otp" maxlength="6" pattern="[0-9]{6}" placeholder="Enter 6-digit code">
                            </div>
                        </div>
                        <button type="submit" name="verify_otp" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-check-circle"></i> Verify Code
                        </button>
                        <button type="submit" name="resend_otp" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-clockwise"></i> Resend Code
                        </button>
                    </div>

                    <!-- Step 3: New Password -->
                    <div class="step <?= $step === 3 ? 'active' : '' ?>">
                        <p class="text-muted text-center mb-4">Enter your new password</p>
                        <div class="mb-3">
                            <label for="password" class="form-label"><i class="bi bi-lock"></i> New Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" minlength="6" autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye" id="passwordIcon"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label"><i class="bi bi-lock-fill"></i> Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6" autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="bi bi-eye" id="confirmPasswordIcon"></i>
                                </button>
                            </div>
                            <small class="text-muted">Must be 6+ characters</small>
                        </div>
                        <button type="submit" name="reset_password" class="btn btn-primary w-100">
                            <i class="bi bi-key"></i> Reset Password
                        </button>
                    </div>

                    <!-- Step 4: Success -->
                    <div class="step <?= $step === 4 ? 'active' : '' ?>">
                        <div class="text-center">
                            <i class="bi bi-check-circle" style="font-size: 4rem; color: var(--button);"></i>
                            <h5 class="mb-3" style="color: var(--headline);">Password Reset Complete!</h5>
                            <p class="text-muted mb-4">Your password has been successfully updated.</p>
                            <a href="login.php" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right"></i> Go to Login
                            </a>
                        </div>
                    </div>
                </form>

                <?php if ($step < 4): ?>
                <div class="text-center mt-3">
                    <a href="login.php" style="color: var(--headline); text-decoration: none;">
                        <i class="bi bi-arrow-left"></i> Back to Login
                    </a>
                </div>
                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Email Loader Modal -->
        <div class="email-loader-modal" id="emailLoaderModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); display: none; justify-content: center; align-items: center; z-index: 9999;">
            <div class="email-loader-content" style="background: white; padding: 2rem; border-radius: 1rem; text-align: center; min-width: 300px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
                <div class="email-loader-spinner" style="display: inline-block; width: 50px; height: 50px; border: 4px solid #f3f3f3; border-top: 4px solid var(--headline); border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 1rem;"></div>
                <h5 style="color: var(--headline); margin-bottom: 0.5rem;">Sending Reset Code</h5>
                <p style="color: var(--paragraph); margin: 0;">Please wait while we send your password reset code...</p>
            </div>
        </div>
        
        <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        </style>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        
        <script>
            const form = document.querySelector('form');
            const emailLoaderModal = document.getElementById('emailLoaderModal');
            const sendOtpBtn = document.querySelector('button[name="send_otp"]');
            
            // Add hidden input when send_otp button is clicked
            if (sendOtpBtn) {
                sendOtpBtn.addEventListener('click', function(e) {
                    // Remove any existing inputs
                    const existing = form.querySelector('input[name="send_otp"]');
                    if (existing) existing.remove();
                    const existingVerify = form.querySelector('input[name="verify_otp"]');
                    if (existingVerify) existingVerify.remove();
                    
                    // Add hidden input
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'send_otp';
                    hiddenInput.value = '1';
                    form.appendChild(hiddenInput);
                    
                    console.log('Added send_otp input');
                });
            }
            
            // Handle verify OTP button
            const verifyOtpBtn = document.querySelector('button[name="verify_otp"]');
            if (verifyOtpBtn) {
                verifyOtpBtn.addEventListener('click', function(e) {
                    // Remove send_otp input if exists
                    const existing = form.querySelector('input[name="send_otp"]');
                    if (existing) existing.remove();
                    
                    // Add verify_otp hidden input
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'verify_otp';
                    hiddenInput.value = '1';
                    form.appendChild(hiddenInput);
                    
                    console.log('Added verify_otp input');
                });
            }
            
            // Handle resend OTP button
            const resendOtpBtn = document.querySelector('button[name="resend_otp"]');
            if (resendOtpBtn) {
                resendOtpBtn.addEventListener('click', function(e) {
                    // Remove other inputs
                    const existing = form.querySelector('input[name="send_otp"]');
                    if (existing) existing.remove();
                    const existingVerify = form.querySelector('input[name="verify_otp"]');
                    if (existingVerify) existingVerify.remove();
                    
                    // Add resend_otp hidden input
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'resend_otp';
                    hiddenInput.value = '1';
                    form.appendChild(hiddenInput);
                    
                    console.log('Added resend_otp input');
                });
            }
            
            // Handle reset password button
            const resetPasswordBtn = document.querySelector('button[name="reset_password"]');
            if (resetPasswordBtn) {
                resetPasswordBtn.addEventListener('click', function(e) {
                    // Remove other inputs
                    const existing = form.querySelector('input[name="send_otp"]');
                    if (existing) existing.remove();
                    const existingVerify = form.querySelector('input[name="verify_otp"]');
                    if (existingVerify) existingVerify.remove();
                    
                    // Add reset_password hidden input
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'reset_password';
                    hiddenInput.value = '1';
                    form.appendChild(hiddenInput);
                    
                    console.log('Added reset_password input');
                });
            }
            
            form.addEventListener('submit', function(e) {
                const submitBtn = e.submitter;
                console.log('Form submitted with button:', submitBtn ? submitBtn.name : 'unknown');
                if (submitBtn && (submitBtn.name === 'send_otp' || submitBtn.name === 'resend_otp')) {
                    emailLoaderModal.style.display = 'flex';
                    submitBtn.disabled = true;
                    
                    // SECURITY - Client-side rate limiting (commented out for development)
                    // if (submitBtn.name === 'resend_otp') {
                    //     submitBtn.disabled = true;
                    //     setTimeout(() => { submitBtn.disabled = false; }, 60000);
                    // }
                }
            });
            
            // Password toggle functionality
            const togglePassword = document.getElementById('togglePassword');
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            
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
            
            if (toggleConfirmPassword) {
                toggleConfirmPassword.addEventListener('click', function() {
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
            }
        </script>
        
        <?php if ($success && $step === 4): ?>
        <script>
            window.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Password Reset Complete!',
                    text: 'Your password has been successfully updated.',
                    confirmButtonColor: '<?= $colors['button'] ?>',
                    confirmButtonText: 'Go to Login'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'login.php';
                    }
                });
            });
        </script>
        <?php endif; ?>
    </body>
    </html>
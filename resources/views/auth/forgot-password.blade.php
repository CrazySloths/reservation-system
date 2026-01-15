<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - LGU1</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
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
            background: linear-gradient(90deg, var(--highlight) 0%, var(--secondary) 100%);
            color: var(--button-text);
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
                                <img src="{{ asset('assets/images/logo.png') }}" alt="LGU1 Logo" style="max-width: 80px; margin-bottom: 1rem;">
                                <h2 style="color: var(--headline); font-family: 'Merriweather', serif; font-weight: 700;">Reset Password</h2>
                                <p class="text-muted"><i class="bi bi-key-fill"></i> Password Recovery</p>
                            </div>
            
            @if(session('error'))
                <script>
                    window.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: '{{ session('error') }}',
                            confirmButtonColor: '#faae2b',
                        });
                    });
                </script>
            @endif
            
            @if(session('success_message'))
                <script>
                    window.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: '{{ session('success_message') }}',
                            confirmButtonColor: '#faae2b',
                        });
                    });
                </script>
            @endif

            <form method="POST">
                @csrf
                <!-- Step 1: Enter Email -->
                <div class="step {{ (session('step') ?? 1) === 1 ? 'active' : '' }}">
                    <p class="text-muted text-center mb-4">Enter your email address to receive a verification code</p>
                    <div class="mb-3">
                        <label for="email" class="form-label"><i class="bi bi-envelope"></i> Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                        </div>
                    </div>
                    <button type="submit" name="send_otp" value="1" class="btn btn-primary w-100">
                        <i class="bi bi-send"></i> Send Verification Code
                    </button>
                </div>

                <!-- Step 2: Verify OTP -->
                <div class="step {{ (session('step') ?? 1) === 2 ? 'active' : '' }}">
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
                <div class="step {{ (session('step') ?? 1) === 3 ? 'active' : '' }}">
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
                <div class="step {{ (session('step') ?? 1) === 4 ? 'active' : '' }}">
                    <div class="text-center">
                        <i class="bi bi-check-circle" style="font-size: 4rem; color: var(--button);"></i>
                        <h5 class="mb-3" style="color: var(--headline);">Password Reset Complete!</h5>
                        <p class="text-muted mb-4">Your password has been successfully updated.</p>
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Go to Login
                        </a>
                    </div>
                </div>
            </form>

            @if((session('step') ?? 1) < 4)
            <div class="text-center mt-3">
                <a href="{{ route('login') }}" style="color: var(--headline); text-decoration: none;">
                    <i class="bi bi-arrow-left"></i> Back to Login
                </a>
            </div>
            @endif
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
        
        // Handle form submission and show loader for email sending
        form.addEventListener('submit', function(e) {
            const submitBtn = e.submitter;
            console.log('Form submitted with button:', submitBtn ? submitBtn.name : 'unknown');
            if (submitBtn && (submitBtn.name === 'send_otp' || submitBtn.name === 'resend_otp')) {
                emailLoaderModal.style.display = 'flex';
                submitBtn.disabled = true;
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
    
    @if(session('step') === 4)
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Password Reset Complete!',
                text: 'Your password has been successfully updated.',
                confirmButtonColor: '#faae2b',
                confirmButtonText: 'Go to Login'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route('login') }}';
                }
            });
        });
    </script>
    @endif
</body>
</html>


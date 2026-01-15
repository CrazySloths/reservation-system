@extends('layouts.auth')

@section('title', 'LGU1 - Login')

@section('content')
<div id="loginContainer">
    <div class="text-center mb-4">
        <img src="{{ asset('assets/images/logo.png') }}" alt="LGU1 Logo" class="login-logo">
        <h2 style="color: var(--headline); font-family: 'Merriweather', serif; font-weight: 700;">Sign In</h2>
        <p class="text-muted"><i class="bi bi-shield-check-fill"></i> Secure Login</p>
    </div>

    <!-- Session Timeout Message -->
    @if(request()->get('timeout') == '1')
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-clock-history"></i>
        <strong>Session Expired!</strong> Your session has expired due to inactivity. Please login again.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Error Messages -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Step 1: Email/Password Form -->
    <form id="loginForm" style="display: block;">
        <div class="mb-3">
            <label for="email" class="form-label"><i class="bi bi-envelope"></i> Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" 
                       class="form-control" 
                       id="email" 
                       name="email" 
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

    <!-- Step 2: OTP Verification Form (Hidden initially) -->
    <div id="otpForm" style="display: none;">
        <div class="text-center mb-4">
            <i class="bi bi-shield-check" style="font-size: 3rem; color: var(--highlight);"></i>
            <h4 style="color: var(--headline); margin-top: 1rem;">Verify Your Identity</h4>
            <p class="text-muted">Enter the 6-digit code sent to your email</p>
        </div>
        
        <form id="otpVerifyForm">
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
                <small class="text-muted">Code expires in 1 minute</small>
            </div>

            <button type="submit" class="btn btn-primary w-100" id="verifyBtn">
                <i class="bi bi-check-circle"></i> Verify & Sign In
            </button>
            
            <button type="button" class="btn btn-outline-warning w-100 mt-2" id="resendOtpBtn">
                <i class="bi bi-arrow-clockwise"></i> Resend OTP
            </button>
            
            <button type="button" class="btn btn-outline-secondary w-100 mt-2" id="backToLogin">
                <i class="bi bi-arrow-left"></i> Back to Login
            </button>
        </form>
    </div>

    <div class="divider">or</div>
    <div class="text-center">
        <a href="{{ route('register') }}" class="register-link"><i class="bi bi-person-plus"></i> Create New Account</a>
    </div>
    <div class="text-center mt-2">
        <a href="{{ route('password.request') }}" class="register-link"><i class="bi bi-key"></i> Forgot Password?</a>
    </div>
</div>

<!-- Loader Overlay -->
<div class="loader-overlay" id="loaderOverlay">
    <div class="loader-content">
        <div class="loader-spinner"></div>
        <h5 style="color: var(--headline); margin-bottom: 0.5rem;">Sending Verification Code</h5>
        <p class="text-muted mb-0">Please wait while we send the OTP to your email...</p>
    </div>
</div>

@push('styles')
<style>
    .login-logo {
        display: block;
        margin: 0 auto 1.2rem auto;
        max-width: 90px;
        filter: drop-shadow(0 2px 8px #00473e22);
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
        border-top: 4px solid #00473e;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 1rem;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const otpForm = document.getElementById('otpForm');
    const otpVerifyForm = document.getElementById('otpVerifyForm');
    const loaderOverlay = document.getElementById('loaderOverlay');
    const loginBtn = document.getElementById('loginBtn');
    const verifyBtn = document.getElementById('verifyBtn');
    const resendOtpBtn = document.getElementById('resendOtpBtn');
    const backToLoginBtn = document.getElementById('backToLogin');
    
    // CSRF Token Management - Prevent stale token issues
    let csrfToken = '{{ csrf_token() }}';
    
    // Refresh CSRF token function
    async function refreshCSRFToken() {
        try {
            const response = await fetch('/csrf-token', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            if (data.csrf_token) {
                csrfToken = data.csrf_token;
                console.log('CSRF token refreshed');
            }
        } catch (error) {
            console.error('Failed to refresh CSRF token:', error);
        }
    }
    
    // Refresh token every 30 seconds (aggressive refresh for login/OTP process)
    let tokenRefreshInterval = setInterval(refreshCSRFToken, 30000);
    
    // Refresh token when page becomes visible after being hidden (tab switching)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            refreshCSRFToken();
        }
    });
    
    // Refresh token when window regains focus
    window.addEventListener('focus', function() {
        refreshCSRFToken();
    });
    
    // Handle CSRF token mismatch errors - SILENTLY refresh token, no modals
    async function handleCSRFError() {
        // Silently refresh token without any user notification
        await refreshCSRFToken();
        // Token refreshed, ready for next attempt
    }
    
    // Function to show OTP form and start aggressive token refresh
    function showOTPForm() {
        loginForm.style.display = 'none';
        otpForm.style.display = 'block';
        document.getElementById('otp').focus();
        
        // Refresh token immediately when OTP form shows
        refreshCSRFToken();
        
        // More aggressive refresh while on OTP screen (every 15 seconds)
        clearInterval(tokenRefreshInterval);
        tokenRefreshInterval = setInterval(refreshCSRFToken, 15000);
    }
    
    // Password toggle
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
    
    // Step 1: Login with Email/Password
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        
        loginBtn.disabled = true;
        loginBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Sending OTP...';
        loaderOverlay.style.display = 'flex';
        
        fetch('{{ route("login.post") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email, password })
        })
        .then(async response => {
            // Check for CSRF token mismatch (419 status) - Silently refresh and retry
            if (response.status === 419) {
                await handleCSRFError();
                // Retry the request with fresh token
                const retryResponse = await fetch('{{ route("login.post") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });
                return retryResponse.json();
            }
            return response.json();
        })
        .then(data => {
            loaderOverlay.style.display = 'none';
            loginBtn.disabled = false;
            loginBtn.innerHTML = '<i class="bi bi-box-arrow-in-right"></i> Sign In';
            
            if (data.success) {
                // Switch directly to OTP form (no popup)
                showOTPForm();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    text: data.message,
                    confirmButtonColor: '#fa5246'
                });
            }
        })
        .catch(error => {
            loaderOverlay.style.display = 'none';
            loginBtn.disabled = false;
            loginBtn.innerHTML = '<i class="bi bi-box-arrow-in-right"></i> Sign In';
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Something went wrong. Please try again.',
                confirmButtonColor: '#fa5246'
            });
        });
    });
    
    // Step 2: Verify OTP
    otpVerifyForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const otp = document.getElementById('otp').value;
        
        verifyBtn.disabled = true;
        verifyBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Verifying...';
        
        // Refresh token immediately before submission
        await refreshCSRFToken();
        
        fetch('{{ route("login.verify-otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ otp })
        })
        .then(async response => {
            // Check for CSRF token mismatch - Silently refresh and retry
            if (response.status === 419) {
                await handleCSRFError();
                // Retry with fresh token
                const retryResponse = await fetch('{{ route("login.verify-otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ otp })
                });
                return retryResponse.json();
            }
            return response.json();
        })
        .then(data => {
            verifyBtn.disabled = false;
            verifyBtn.innerHTML = '<i class="bi bi-check-circle"></i> Verify & Sign In';
            
            if (data.success) {
                // Redirect directly to dashboard without popup
                window.location.href = data.redirect;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid OTP',
                    text: data.message,
                    confirmButtonColor: '#fa5246'
                });
            }
        })
        .catch(error => {
            verifyBtn.disabled = false;
            verifyBtn.innerHTML = '<i class="bi bi-check-circle"></i> Verify & Sign In';
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Something went wrong. Please try again.',
                confirmButtonColor: '#fa5246'
            });
        });
    });
    
    // Resend OTP button
    resendOtpBtn.addEventListener('click', async function() {
        resendOtpBtn.disabled = true;
        resendOtpBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Sending...';
        
        // Refresh token before resending
        await refreshCSRFToken();
        
        fetch('{{ route("login.resend-otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            // Check for CSRF token mismatch - Silently refresh and retry
            if (response.status === 419) {
                await handleCSRFError();
                // Retry with fresh token
                const retryResponse = await fetch('{{ route("login.resend-otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                return retryResponse.json();
            }
            return response.json();
        })
        .then(data => {
            resendOtpBtn.disabled = false;
            resendOtpBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Resend OTP';
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'OTP Sent!',
                    text: data.message,
                    confirmButtonColor: '#00473e',
                    timer: 3000
                });
                
                // Clear OTP input and focus
                document.getElementById('otp').value = '';
                document.getElementById('otp').focus();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: data.message,
                    confirmButtonColor: '#fa5246'
                });
            }
        })
        .catch(error => {
            resendOtpBtn.disabled = false;
            resendOtpBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Resend OTP';
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Something went wrong. Please try again.',
                confirmButtonColor: '#fa5246'
            });
        });
    });
    
    // Back to login button
    backToLoginBtn.addEventListener('click', function() {
        otpForm.style.display = 'none';
        loginForm.style.display = 'block';
        document.getElementById('email').focus();
        
        // Reset to normal refresh interval (30 seconds)
        clearInterval(tokenRefreshInterval);
        tokenRefreshInterval = setInterval(refreshCSRFToken, 30000);
    });
});
</script>
@endpush
@endsection

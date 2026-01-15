@extends('layouts.auth')

@section('title', 'Register - LGU1')

@section('hero-title', 'Registration Portal')

@section('hero-description', 'Create your account to access LGU1 services and systems. Join our digital government platform for efficient service delivery.')

@section('content')
<div x-data="registerForm({{ $step }}, {{ $verificationEmailSent ? 'true' : 'false' }})">
    <div class="text-center mb-4">
        <img src="{{ asset('assets/images/logo.png') }}" alt="LGU1 Logo" style="max-width: 80px; margin-bottom: 1rem;">
        <h2 style="color: var(--headline); font-family: 'Merriweather', serif; font-weight: 700;">Create Account</h2>
        <p class="text-muted"><i class="bi bi-people-fill"></i> For Official Use of LGU1</p>
    </div>

    <!-- Step Indicator -->
    <div class="step-indicator">
        <div class="step-item" :class="{ 'active': currentStep === 1 }">
            <div class="step-number">1</div>
            <span>Account</span>
        </div>
        <div class="step-line" :class="{ 'active': currentStep > 1 }"></div>
        <div class="step-item" :class="{ 'active': currentStep === 2 }">
            <div class="step-number">2</div>
            <span>Personal</span>
        </div>
        <div class="step-line" :class="{ 'active': currentStep > 2 }"></div>
        <div class="step-item" :class="{ 'active': currentStep === 3 }">
            <div class="step-number">3</div>
            <span>Address</span>
        </div>
        <div class="step-line" :class="{ 'active': currentStep > 3 }"></div>
        <div class="step-item" :class="{ 'active': currentStep === 4 }">
            <div class="step-number">4</div>
            <span>ID</span>
        </div>
        <div class="step-line" :class="{ 'active': currentStep > 4 }"></div>
        <div class="step-item" :class="{ 'active': currentStep === 5 }">
            <div class="step-number">5</div>
            <span>Verify</span>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf
        
        <!-- Hidden field for AI verification data -->
        <input type="hidden" id="ai_verification_data" name="ai_verification_data" value="">

        <!-- Registration Type Selection -->
        <div class="mb-4">
            <label for="registration_type" class="form-label" style="color: var(--headline); font-family: 'Merriweather', serif; font-weight: 700;"><i class="bi bi-person-check"></i> Registration Type</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-list-ul"></i></span>
                <select class="form-control" id="registration_type" name="registration_type" required>
                    <option value="facility_user" selected>Citizen - Public facilities reservation</option>
                    <option value="applicant">Housing Applicant - Housing & resettlement programs</option>
                    <option value="utility_customer">Utility Customer - Water & electricity billing</option>
                    <option value="land_citizen">Citizen - Land Registration and Titling System</option>
                    <option value="resident">Resident - Community infrastructure maintenance</option>
                    <option value="road_resident">Resident - Road and Transportation Infrastructure Monitoring</option>
                    <option value="property_owner">Property Owner - Urban Planning & Development</option>
                </select>
            </div>
            <small class="text-muted">Select the type that best describes your primary need for LGU services</small>
        </div>

        <!-- Step 1: Account Information -->
        <div class="step" x-show="currentStep === 1" x-cloak>
            <h5 class="mb-3 text-center" style="color: var(--headline);">Account Information</h5>
            <div class="mb-3">
                <label for="username" class="form-label"><i class="bi bi-person-badge"></i> Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" id="username" name="username" required maxlength="50" value="{{ old('username') }}">
                </div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label"><i class="bi bi-envelope-paper"></i> Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" required maxlength="100" value="{{ old('email') }}">
                </div>
                <div id="emailError" class="text-danger mt-1" style="display: none; font-size: 0.875rem;"></div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label"><i class="bi bi-lock"></i> Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input :type="showPassword ? 'text' : 'password'" class="form-control" id="password" name="password" autocomplete="new-password" required minlength="6">
                    <button class="btn btn-outline-secondary" type="button" @click="showPassword = !showPassword">
                        <i class="bi" :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"></i>
                    </button>
                </div>
                <small class="text-muted">Must be 6+ characters</small>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label"><i class="bi bi-lock-fill"></i> Confirm Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input :type="showConfirmPassword ? 'text' : 'password'" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required minlength="6">
                    <button class="btn btn-outline-secondary" type="button" @click="showConfirmPassword = !showConfirmPassword">
                        <i class="bi" :class="showConfirmPassword ? 'bi-eye-slash' : 'bi-eye'"></i>
                    </button>
                </div>
            </div>
            <div class="step-buttons">
                <button type="button" class="btn btn-primary btn-step" id="step1NextBtn" @click="checkEmailAndProceed()">
                    <span id="step1BtnText">Next <i class="bi bi-arrow-right"></i></span>
                    <span id="step1BtnLoader" style="display: none;">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Checking...
                    </span>
                </button>
            </div>
        </div>

        <!-- Step 2: Personal Information -->
        <div class="step" x-show="currentStep === 2" x-cloak>
            <h5 class="mb-3 text-center" style="color: var(--headline);">Personal Information</h5>
            <div class="mb-3">
                <label for="full_name" class="form-label"><i class="bi bi-person-vcard"></i> Full Name</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                    <input type="text" class="form-control" id="full_name" name="full_name" required maxlength="100" value="{{ old('full_name') }}">
                </div>
            </div>
            <div class="mb-3">
                <label for="birthdate" class="form-label"><i class="bi bi-calendar-date"></i> Birthdate</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                    <input type="date" class="form-control" id="birthdate" name="birthdate" required value="{{ old('birthdate') }}">
                </div>
            </div>
            <div class="mb-3">
                <label for="mobile_number" class="form-label"><i class="bi bi-phone"></i> Mobile Number</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-phone"></i></span>
                    <input type="tel" class="form-control" id="mobile_number" name="mobile_number" required placeholder="09xxxxxxxxx" pattern="^09\d{9}$" maxlength="11" value="{{ old('mobile_number') }}">
                </div>
                <small class="text-muted">Must be 11 digits starting with 09</small>
                <div id="mobileError" class="text-danger mt-1" style="display: none; font-size: 0.875rem;"></div>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label"><i class="bi bi-gender-ambiguous"></i> Gender</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <select class="form-control" id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="civil_status" class="form-label"><i class="bi bi-heart"></i> Civil Status</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-hearts"></i></span>
                    <select class="form-control" id="civil_status" name="civil_status" required>
                        <option value="">Select Civil Status</option>
                        <option value="single">Single</option>
                        <option value="married">Married</option>
                        <option value="divorced">Divorced</option>
                        <option value="widowed">Widowed</option>
                        <option value="separated">Separated</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="nationality" class="form-label"><i class="bi bi-flag"></i> Nationality</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-globe"></i></span>
                    <input type="text" class="form-control" id="nationality" name="nationality" required maxlength="50" value="{{ old('nationality', 'Filipino') }}">
                </div>
            </div>
            <div class="step-buttons">
                <button type="button" class="btn btn-outline-secondary btn-step" @click="prevStep()"><i class="bi bi-arrow-left"></i> Previous</button>
                <button type="button" class="btn btn-primary btn-step" id="step2NextBtn" @click="checkMobileAndProceed()">
                    <span id="step2BtnText">Next <i class="bi bi-arrow-right"></i></span>
                    <span id="step2BtnLoader" style="display: none;">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Checking...
                    </span>
                </button>
            </div>
        </div>

        <!-- Step 3: Address Information -->
        <div class="step" x-show="currentStep === 3" x-cloak>
            <h5 class="mb-3 text-center" style="color: var(--headline);">Address Information</h5>
            
            <!-- Current Residential Address (Text Field) -->
            <div class="mb-3">
                <label for="current_address" class="form-label"><i class="bi bi-geo"></i> Current Residential Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-house-door"></i></span>
                    <textarea class="form-control" id="current_address" name="current_address" required rows="3" placeholder="Enter your complete residential address (House No., Street, Subdivision, etc.)">{{ old('current_address') }}</textarea>
                </div>
                <small class="text-muted">Example: Blk 1 Lot 2, Sample Street, XYZ Subdivision</small>
            </div>

            <!-- Region Dropdown -->
            <div class="mb-3">
                <label for="region_id" class="form-label"><i class="bi bi-globe"></i> Region</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-globe"></i></span>
                    <select class="form-control" id="region_id" name="region_id" required>
                        <option value="">Select Region</option>
                    </select>
                </div>
            </div>

            <!-- Province Dropdown -->
            <div class="mb-3">
                <label for="province_id" class="form-label"><i class="bi bi-map"></i> Province</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-map"></i></span>
                    <select class="form-control" id="province_id" name="province_id" required disabled>
                        <option value="">Select Region First</option>
                    </select>
                </div>
            </div>

            <!-- City/Municipality Dropdown -->
            <div class="mb-3">
                <label for="city_id" class="form-label"><i class="bi bi-building"></i> City/Municipality</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                    <select class="form-control" id="city_id" name="city_id" required disabled>
                        <option value="">Select Province First</option>
                    </select>
                </div>
            </div>

            <!-- District Dropdown (ALL cities have districts) -->
            <div class="mb-3">
                <label for="district_id" class="form-label"><i class="bi bi-geo-alt"></i> District</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                    <select class="form-control" id="district_id" name="district_id" required disabled>
                        <option value="">Select City First</option>
                    </select>
                </div>
                <small class="text-muted">Districts help organize barangays within the city</small>
            </div>

            <!-- Barangay Dropdown -->
            <div class="mb-3">
                <label for="barangay_id" class="form-label"><i class="bi bi-house"></i> Barangay</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-house"></i></span>
                    <select class="form-control" id="barangay_id" name="barangay_id" required disabled>
                        <option value="">Select District First</option>
                    </select>
                </div>
            </div>

            <!-- Zip Code -->
            <div class="mb-3">
                <label for="zip_code" class="form-label"><i class="bi bi-mailbox"></i> Zip Code</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="text" class="form-control" id="zip_code" name="zip_code" required maxlength="10" pattern="[0-9]{4,10}" placeholder="e.g. 1100" value="{{ old('zip_code') }}">
                </div>
            </div>

            <div class="step-buttons">
                <button type="button" class="btn btn-outline-secondary btn-step" @click="prevStep()"><i class="bi bi-arrow-left"></i> Previous</button>
                <button type="button" class="btn btn-primary btn-step" @click="nextStep()">Next <i class="bi bi-arrow-right"></i></button>
            </div>
        </div>

        <!-- Step 4: ID Verification -->
        <div class="step" x-show="currentStep === 4" x-cloak>
            <h5 class="mb-3 text-center" style="color: var(--headline);">ID Verification</h5>
            <div class="mb-3">
                <label for="valid_id_type" class="form-label"><i class="bi bi-credit-card"></i> Valid ID Type</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                    <select class="form-control" id="valid_id_type" name="valid_id_type" required>
                        <option value="">Select Valid ID</option>
                        <option value="SSS ID">SSS ID</option>
                        <option value="UMID">UMID</option>
                        <option value="PhilHealth ID">PhilHealth ID</option>
                        <option value="TIN ID">TIN ID</option>
                        <option value="Passport">Passport</option>
                        <option value="Driver's License">Driver's License</option>
                        <option value="Voter's ID">Voter's ID</option>
                        <option value="PRC ID">PRC ID</option>
                        <option value="Postal ID">Postal ID</option>
                        <option value="Barangay ID">Barangay ID</option>
                        <option value="Senior Citizen ID">Senior Citizen ID</option>
                        <option value="PWD ID">PWD ID</option>
                        <option value="National ID (PhilSys)">National ID (PhilSys)</option>
                        <option value="School ID">School ID</option>
                        <option value="Company ID">Company ID</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="valid_id_front_image" class="form-label"><i class="bi bi-card-image"></i> Upload Valid ID - Front Side</label>
                <input type="file" class="form-control" id="valid_id_front_image" name="valid_id_front_image" accept="image/*" required>
                <small class="text-muted">Upload front side (JPG, PNG, max 5MB)</small>
                <!-- Preview Area for Front -->
                <div id="frontImagePreview" style="display: none; margin-top: 10px;">
                    <img id="frontPreviewImg" src="" alt="ID Front Preview" style="max-width: 100%; height: auto; border-radius: 8px; border: 2px solid var(--highlight); margin-top: 10px;">
                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="removeFrontImage">
                        <i class="bi bi-trash"></i> Remove Front Image
                    </button>
                </div>
            </div>
            <div class="mb-3">
                <label for="valid_id_back_image" class="form-label"><i class="bi bi-card-image"></i> Upload Valid ID - Back Side</label>
                <input type="file" class="form-control" id="valid_id_back_image" name="valid_id_back_image" accept="image/*" required>
                <small class="text-muted">Upload back side (JPG, PNG, max 5MB)</small>
                <!-- Preview Area for Back -->
                <div id="backImagePreview" style="display: none; margin-top: 10px;">
                    <img id="backPreviewImg" src="" alt="ID Back Preview" style="max-width: 100%; height: auto; border-radius: 8px; border: 2px solid var(--highlight); margin-top: 10px;">
                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="removeBackImage">
                        <i class="bi bi-trash"></i> Remove Back Image
                    </button>
                </div>
            </div>
            <div class="mb-3">
                <label for="selfie_with_id_image" class="form-label"><i class="bi bi-person-circle"></i> Selfie with ID</label>
                <input type="file" class="form-control" id="selfie_with_id_image" name="selfie_with_id_image" accept="image/*" required>
                <small class="text-muted">Upload selfie with ID (JPG, PNG, max 5MB)</small>
                <!-- Preview Area for Selfie -->
                <div id="selfiePreview" style="display: none; margin-top: 10px;">
                    <img id="selfieImg" src="" alt="Selfie Preview" style="max-width: 100%; height: auto; border-radius: 8px; border: 2px solid var(--highlight); margin-top: 10px;">
                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="removeSelfie">
                        <i class="bi bi-trash"></i> Remove Selfie
                    </button>
                </div>
            </div>
            <div class="step-buttons">
                <button type="button" class="btn btn-outline-secondary btn-step" @click="prevStep()"><i class="bi bi-arrow-left"></i> Previous</button>
                <button type="button" class="btn btn-primary btn-step" @click="submitRegistration()" :disabled="isVerifying">
                    <span x-show="!isVerifying">Register <i class="bi bi-check-circle"></i></span>
                    <span x-show="isVerifying">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Verifying...
                    </span>
                </button>
            </div>
        </div>
    </form>

    <!-- Step 5: Email Verification -->
    <form method="POST" action="{{ route('register.verify-otp') }}" x-show="currentStep === 5" x-cloak>
        @csrf
        <div class="step">
            <h5 class="mb-3 text-center" style="color: var(--headline);">Email Verification</h5>
            @if($verificationEmailSent)
                <div class="alert alert-info text-center">
                    <i class="bi bi-envelope-check"></i> We've sent a verification code to<br>
                    <strong>{{ session('pending_user_email') }}</strong>
                </div>
            @endif
            <div class="mb-3">
                <label for="otp" class="form-label"><i class="bi bi-shield-check"></i> Enter Verification Code</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-123"></i></span>
                    <input type="text" class="form-control" id="otp" name="otp" required maxlength="6" pattern="[0-9]{6}" placeholder="Enter 6-digit code" autocomplete="off" :disabled="!termsAccepted">
                </div>
                <small class="text-muted">Check your email for the 6-digit verification code</small>
            </div>
            <div class="text-center mb-3">
                <button type="button" class="btn btn-outline-info btn-sm" @click="resendVerificationEmail()">
                    <i class="bi bi-arrow-clockwise"></i> Resend Verification Email
                </button>
                <br><small class="text-muted">Didn't receive the email? Click above to resend.</small>
            </div>
            
            <!-- Terms Acceptance Status -->
            <div class="mb-3">
                <div class="alert" :class="termsAccepted ? 'alert-success' : 'alert-warning'" role="alert">
                    <i class="bi" :class="termsAccepted ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'"></i>
                    <span x-show="!termsAccepted">
                        Please read and accept the Terms and Conditions to continue
                    </span>
                    <span x-show="termsAccepted">
                        ✓ Terms and Conditions accepted
                    </span>
                </div>
                <div class="d-grid">
                    <button type="button" class="btn" :class="termsAccepted ? 'btn-outline-secondary' : 'btn-warning'" @click="showTermsModal = true">
                        <i class="bi bi-file-text"></i> <span x-text="termsAccepted ? 'View Terms Again' : 'Read Terms and Conditions'"></span>
                    </button>
                </div>
            </div>
            
            <div class="step-buttons">
                <button type="submit" class="btn btn-primary btn-step" :disabled="!termsAccepted">
                    <i class="bi bi-shield-check"></i> Verify Email
                </button>
            </div>
        </div>
        
        <!-- Terms and Conditions Modal -->
        <div class="terms-modal-overlay" x-show="showTermsModal" x-cloak @click.self="showTermsModal = false">
            <div class="terms-modal">
                <div class="terms-modal-header">
                    <h5><i class="bi bi-file-text"></i> Terms and Conditions & Data Privacy Policy</h5>
                    <button type="button" class="btn-close-modal" @click="showTermsModal = false">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                
                <div class="terms-modal-body">
                    <h6>Terms and Conditions</h6>
                    <p><strong>Effective Date:</strong> November 2025</p>
                    
                    <p>Welcome to the Local Government Unit 1 (LGU1) Registration Portal. By creating an account and using our services, you agree to comply with and be bound by the following terms and conditions.</p>
                    
                    <h6>1. Account Registration</h6>
                    <p>You must provide accurate, complete, and current information during the registration process. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</p>
                    
                    <h6>2. Use of Services</h6>
                    <p>Our services are provided for legitimate government transactions and interactions with LGU1. You agree not to misuse the platform for any illegal, fraudulent, or unauthorized purposes.</p>
                    
                    <h6>3. Identity Verification</h6>
                    <p>As part of our registration process, we use AI-powered identity verification to ensure the authenticity of user accounts. By uploading your identification documents and selfie, you consent to this verification process.</p>
                    
                    <h6>4. User Responsibilities</h6>
                    <ul>
                        <li>Provide truthful and accurate information</li>
                        <li>Keep your account credentials secure</li>
                        <li>Notify us immediately of any unauthorized account access</li>
                        <li>Comply with all applicable laws and regulations</li>
                    </ul>
                    
                    <h6>5. Prohibited Activities</h6>
                    <ul>
                        <li>Creating multiple accounts with the same identification</li>
                        <li>Using another person's identification documents</li>
                        <li>Attempting to bypass security measures</li>
                        <li>Engaging in fraudulent activities</li>
                    </ul>
                    
                    <h6>6. Account Termination</h6>
                    <p>We reserve the right to suspend or terminate accounts that violate these terms, engage in fraudulent activity, or pose a security risk to our platform.</p>
                    
                    <hr>
                    
                    <h6>Data Privacy Policy</h6>
                    <p><strong>Effective Date:</strong> November 2025</p>
                    
                    <p>We are committed to protecting your privacy and personal data in compliance with the Data Privacy Act of 2012 (Republic Act No. 10173) of the Philippines.</p>
                    
                    <h6>1. Information We Collect</h6>
                    <p>We collect and process the following personal information:</p>
                    <ul>
                        <li><strong>Personal Details:</strong> Full name, birthdate, gender, civil status, nationality</li>
                        <li><strong>Contact Information:</strong> Email address, mobile number, current address</li>
                        <li><strong>Identification Documents:</strong> Government-issued ID images (front and back)</li>
                        <li><strong>Biometric Data:</strong> Facial image (selfie) for identity verification</li>
                        <li><strong>Location Data:</strong> Region, province, city, district, barangay</li>
                    </ul>
                    
                    <h6>2. How We Use Your Information</h6>
                    <p>Your personal information is used for:</p>
                    <ul>
                        <li>Account creation and verification</li>
                        <li>Identity authentication using AI technology</li>
                        <li>Fraud prevention and security</li>
                        <li>Communication regarding your account and LGU services</li>
                        <li>Compliance with legal and regulatory requirements</li>
                    </ul>
                    
                    <h6>3. AI-Powered Verification</h6>
                    <p>We use artificial intelligence (Face++ API) to verify that the face in your identification document matches your selfie. This helps prevent identity theft and ensures account security. Your biometric data is processed securely and is not used for any other purpose.</p>
                    
                    <h6>4. Data Storage and Security</h6>
                    <p>We implement industry-standard security measures to protect your personal information:</p>
                    <ul>
                        <li>Encrypted data transmission (HTTPS/SSL)</li>
                        <li>Secure database storage with access controls</li>
                        <li>Regular security audits and updates</li>
                        <li>Perceptual hashing for duplicate detection (one ID per user)</li>
                    </ul>
                    
                    <h6>5. Data Sharing</h6>
                    <p>We do not sell, trade, or rent your personal information. Your data may be shared only:</p>
                    <ul>
                        <li>With authorized LGU1 personnel for service delivery</li>
                        <li>With AI verification service providers (Face++) for identity verification</li>
                        <li>When required by law or legal process</li>
                    </ul>
                    
                    <h6>6. Your Rights</h6>
                    <p>Under the Data Privacy Act, you have the right to:</p>
                    <ul>
                        <li>Access your personal data</li>
                        <li>Correct inaccurate or incomplete data</li>
                        <li>Object to data processing</li>
                        <li>Request data deletion (subject to legal requirements)</li>
                        <li>Data portability</li>
                    </ul>
                    
                    <h6>7. Data Retention</h6>
                    <p>We retain your personal information for as long as your account is active or as needed to provide services. Identification documents and biometric data are retained for security and compliance purposes.</p>
                    
                    <h6>8. Cookies and Tracking</h6>
                    <p>We use essential cookies for authentication and session management. We do not use tracking cookies for advertising purposes.</p>
                    
                    <h6>9. Contact Information</h6>
                    <p>For privacy-related inquiries or to exercise your rights, contact us at:</p>
                    <p>
                        <strong>Data Protection Officer</strong><br>
                        Local Government Unit 1<br>
                        Email: dpo@lgu1.gov.ph<br>
                        Phone: [Contact Number]
                    </p>
                    
                    <h6>10. Changes to This Policy</h6>
                    <p>We may update this privacy policy periodically. We will notify users of significant changes through email or platform notifications.</p>
                    
                    <hr>
                    
                    <p class="text-center"><em>By checking the box below and proceeding with registration, you acknowledge that you have read, understood, and agree to these Terms and Conditions and Data Privacy Policy.</em></p>
                </div>
                
                <div class="terms-modal-footer">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="termsCheckbox" x-model="termsAccepted">
                        <label class="form-check-label" for="termsCheckbox">
                            <strong>I have read and agree to the Terms and Conditions and Data Privacy Policy</strong>
                        </label>
                    </div>
                    <input type="hidden" name="terms_accepted" :value="termsAccepted ? '1' : '0'">
                    <div class="mt-3 d-grid">
                        <button type="button" class="btn btn-primary btn-lg terms-continue-btn" :disabled="!termsAccepted" :class="{ 'btn-pulse': termsAccepted }" @click="showTermsModal = false">
                            <i class="bi bi-check-circle"></i> Continue
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Step 6: Success -->
    <div x-show="currentStep === 6" x-cloak>
        <div class="step">
            <div class="text-center">
                <div class="mb-4">
                    <i class="bi bi-check-circle" style="font-size: 4rem; color: var(--button);"></i>
                </div>
                <h5 class="mb-3" style="color: var(--headline);">Registration Complete!</h5>
                <p class="text-muted mb-4">Your account has been successfully created and verified. You can now log in to access the LGU1 system.</p>
                <a href="{{ route('login') }}" class="btn btn-primary"><i class="bi bi-box-arrow-in-right"></i> Go to Login</a>
            </div>
        </div>
    </div>

    <div x-show="currentStep < 5" class="divider">or</div>
    <div x-show="currentStep < 5" class="text-center">
        <a href="{{ route('login') }}" class="register-link"><i class="bi bi-box-arrow-in-right"></i> Already have an account? Sign In</a>
    </div>
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    
    /* Step Indicator */
    .step-indicator {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding: 0 1rem;
    }
    
    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 0 0 auto;
    }
    
    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .step-item.active .step-number {
        background: linear-gradient(135deg, var(--button), var(--highlight));
        color: var(--button-text);
        box-shadow: 0 4px 12px rgba(250, 174, 43, 0.3);
    }
    
    .step-item span {
        font-size: 0.75rem;
        color: #6c757d;
        font-weight: 600;
    }
    
    .step-item.active span {
        color: var(--headline);
    }
    
    .step-line {
        flex: 1 1 auto;
        height: 2px;
        background: #e9ecef;
        margin: 0 0.5rem;
        align-self: flex-start;
        margin-top: 20px;
        transition: all 0.3s ease;
    }
    
    .step-line.active {
        background: var(--highlight);
    }
    
    .step-buttons {
        display: flex;
        gap: 1rem;
        justify-content: space-between;
        margin-top: 1.5rem;
    }
    
    .btn-step {
        flex: 1;
    }
    
    @media (max-width: 768px) {
        .step-indicator {
            padding: 0;
        }
        
        .step-item span {
            display: none;
        }
        
        .step-number {
            width: 32px;
            height: 32px;
            font-size: 0.9rem;
        }
        
        .step-line {
            margin-top: 16px;
        }
    }
    
    /* Terms and Conditions Modal */
    .terms-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.75);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        padding: 20px;
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .terms-modal {
        background: white;
        border-radius: 12px;
        max-width: 800px;
        width: 100%;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s ease;
    }
    
    @keyframes slideUp {
        from {
            transform: translateY(50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .terms-modal-header {
        padding: 20px 30px;
        border-bottom: 2px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, var(--button) 0%, var(--button-text) 100%);
        color: white;
        border-radius: 12px 12px 0 0;
    }
    
    .terms-modal-header h5 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
    }
    
    .btn-close-modal {
        background: transparent;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: background 0.3s ease;
    }
    
    .btn-close-modal:hover {
        background: rgba(255, 255, 255, 0.2);
    }
    
    .terms-modal-body {
        padding: 30px;
        overflow-y: auto;
        flex: 1;
        font-size: 0.95rem;
        line-height: 1.6;
    }
    
    .terms-modal-body h6 {
        color: var(--headline);
        font-weight: 600;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        font-size: 1.1rem;
    }
    
    .terms-modal-body h6:first-child {
        margin-top: 0;
    }
    
    .terms-modal-body ul {
        margin-bottom: 1rem;
    }
    
    .terms-modal-body ul li {
        margin-bottom: 0.5rem;
    }
    
    .terms-modal-body hr {
        margin: 2rem 0;
        border-top: 2px solid #e9ecef;
    }
    
    .terms-modal-footer {
        padding: 20px 30px;
        border-top: 2px solid #e9ecef;
        background: #f8f9fa;
        border-radius: 0 0 12px 12px;
    }
    
    .terms-modal-footer .form-check {
        padding: 20px;
        background: linear-gradient(135deg, #fff9e6 0%, #ffffff 100%);
        border-radius: 12px;
        border: 3px solid #ffc107;
        box-shadow: 0 4px 15px rgba(255, 193, 7, 0.2);
        transition: all 0.3s ease;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    
    .terms-modal-footer .form-check:hover {
        border-color: #ff9800;
        box-shadow: 0 6px 20px rgba(255, 152, 0, 0.3);
    }
    
    .terms-modal-footer .form-check-input {
        width: 1.5em;
        height: 1.5em;
        margin-top: 0.25em;
        margin-left: 0;
        cursor: pointer;
        border: 2px solid #ffc107;
        flex-shrink: 0;
    }
    
    .terms-modal-footer .form-check-input:checked {
        background-color: var(--button);
        border-color: var(--button);
        box-shadow: 0 0 10px rgba(46, 125, 50, 0.5);
    }
    
    .terms-modal-footer .form-check-label {
        font-size: 1.1rem;
        cursor: pointer;
        color: #000;
        margin-left: 0;
        flex: 1;
        line-height: 1.6;
    }
    
    .terms-continue-btn {
        font-weight: 600;
        padding: 12px 24px;
        transition: all 0.3s ease;
    }
    
    .terms-continue-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .btn-pulse {
        animation: pulse 2s infinite;
        box-shadow: 0 0 0 0 rgba(46, 125, 50, 0.7);
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(46, 125, 50, 0.7);
        }
        70% {
            box-shadow: 0 0 0 15px rgba(46, 125, 50, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(46, 125, 50, 0);
        }
    }
    
    /* Custom scrollbar for modal body */
    .terms-modal-body::-webkit-scrollbar {
        width: 10px;
    }
    
    .terms-modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .terms-modal-body::-webkit-scrollbar-thumb {
        background: var(--button);
        border-radius: 10px;
    }
    
    .terms-modal-body::-webkit-scrollbar-thumb:hover {
        background: var(--highlight);
    }
    
    @media (max-width: 768px) {
        .terms-modal {
            max-width: 95%;
            max-height: 95vh;
        }
        
        .terms-modal-header {
            padding: 15px 20px;
        }
        
        .terms-modal-body {
            padding: 20px;
            font-size: 0.9rem;
        }
        
        .terms-modal-footer {
            padding: 15px 20px;
        }
    }
</style>
@endpush

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- TensorFlow.js -->
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.11.0"></script>

<!-- Face-API.js - Official library -->
<script defer src="https://cdn.jsdelivr.net/npm/face-api.js/dist/face-api.min.js"></script>

<!-- Google Teachable Machine -->
<script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@0.8.6/dist/teachablemachine-image.min.js"></script>

<!-- Our AI Script -->
<script src="{{ asset('js/azure-ai-verification.js') }}"></script>
<script>
    function registerForm(initialStep, emailSent) {
        return {
            currentStep: initialStep || 1,
            showPassword: false,
            showConfirmPassword: false,
            aiVerificationData: null,
            isVerifying: false,
            termsAccepted: false,
            showTermsModal: false,
            csrfToken: '{{ csrf_token() }}',
            
            init() {
                // CSRF Token Management - Prevent stale token issues
                this.refreshCSRFToken();
                
                // Refresh token every 30 seconds (aggressive for registration process)
                setInterval(() => this.refreshCSRFToken(), 30000);
                
                // Refresh when page becomes visible
                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden) {
                        this.refreshCSRFToken();
                    }
                });
                
                // Check if registration was just completed
                @if(session('verified'))
                    this.currentStep = 6;
                    // Clear session data
                    fetch('/register/clear-session', { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrfToken } });
                @endif
                
                // Watch for step changes to auto-show terms modal
                this.$watch('currentStep', (newStep, oldStep) => {
                    if (newStep === 5 && oldStep !== 5) {
                        // Auto-show terms modal when entering Step 5
                        setTimeout(() => {
                            this.showTermsModal = true;
                        }, 300); // Small delay for smooth transition
                    }
                });
            },
            
            async refreshCSRFToken() {
                try {
                    const response = await fetch('/csrf-token', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (data.csrf_token) {
                        this.csrfToken = data.csrf_token;
                    }
                } catch (error) {
                    console.error('Failed to refresh CSRF token:', error);
                }
            },
            
            async checkEmailAndProceed() {
                const emailInput = document.getElementById('email');
                const email = emailInput.value.trim();
                const emailError = document.getElementById('emailError');
                const step1NextBtn = document.getElementById('step1NextBtn');
                const step1BtnText = document.getElementById('step1BtnText');
                const step1BtnLoader = document.getElementById('step1BtnLoader');
                
                // Clear previous error
                emailError.style.display = 'none';
                emailError.textContent = '';
                emailInput.classList.remove('is-invalid');
                
                // Validate email format
                if (!email || !emailInput.checkValidity()) {
                    emailError.textContent = 'Please enter a valid email address.';
                    emailError.style.display = 'block';
                    emailInput.classList.add('is-invalid');
                    return;
                }
                
                // Show loader
                step1BtnText.style.display = 'none';
                step1BtnLoader.style.display = 'inline-block';
                step1NextBtn.disabled = true;
                
                try {
                    const response = await fetch('/api/check-email', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: JSON.stringify({ email: email })
                    });
                    
                    const data = await response.json();
                    
                    if (data.available) {
                        // Email is available, proceed to next step
                        this.currentStep = 2;
                    } else {
                        // Email is taken, show error
                        emailError.textContent = data.message;
                        emailError.style.display = 'block';
                        emailInput.classList.add('is-invalid');
                        emailInput.focus();
                    }
                } catch (error) {
                    console.error('Email check error:', error);
                    emailError.textContent = 'Error checking email. Please try again.';
                    emailError.style.display = 'block';
                } finally {
                    // Hide loader
                    step1BtnText.style.display = 'inline-block';
                    step1BtnLoader.style.display = 'none';
                    step1NextBtn.disabled = false;
                }
            },
            
            async checkMobileAndProceed() {
                const mobileInput = document.getElementById('mobile_number');
                const mobile = mobileInput.value.trim();
                const mobileError = document.getElementById('mobileError');
                const step2NextBtn = document.getElementById('step2NextBtn');
                const step2BtnText = document.getElementById('step2BtnText');
                const step2BtnLoader = document.getElementById('step2BtnLoader');
                
                // Clear previous error
                mobileError.style.display = 'none';
                mobileError.textContent = '';
                mobileInput.classList.remove('is-invalid');
                
                // Validate mobile format
                if (!mobile || !mobileInput.checkValidity()) {
                    mobileError.textContent = 'Invalid mobile number format. Must be 11 digits starting with 09 (e.g., 09171234567).';
                    mobileError.style.display = 'block';
                    mobileInput.classList.add('is-invalid');
                    return;
                }
                
                // Show loader
                step2BtnText.style.display = 'none';
                step2BtnLoader.style.display = 'inline-block';
                step2NextBtn.disabled = true;
                
                try {
                    const response = await fetch('/api/check-mobile', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: JSON.stringify({ mobile_number: mobile })
                    });
                    
                    const data = await response.json();
                    
                    if (data.available) {
                        // Mobile is available, proceed to next step
                        this.currentStep = 3;
                    } else {
                        // Mobile is taken, show error
                        mobileError.textContent = data.message;
                        mobileError.style.display = 'block';
                        mobileInput.classList.add('is-invalid');
                        mobileInput.focus();
                    }
                } catch (error) {
                    console.error('Mobile check error:', error);
                    mobileError.textContent = 'Error checking mobile number. Please try again.';
                    mobileError.style.display = 'block';
                } finally {
                    // Hide loader
                    step2BtnText.style.display = 'inline-block';
                    step2BtnLoader.style.display = 'none';
                    step2NextBtn.disabled = false;
                }
            },
            
            nextStep() {
                if (this.currentStep < 6) {
                    this.currentStep++;
                }
            },
            
            prevStep() {
                if (this.currentStep > 1 && this.currentStep < 5) {
                    this.currentStep--;
                }
            },
            
            async verifyIDsWithAI() {
                this.isVerifying = true;
                
                try {
                    // Initialize Azure AI Verification (no initialization needed - backend handles it)
                    const aiVerification = new AzureAIVerification();
                    
                    // Get image elements from previews
                    const idFrontImg = document.querySelector('#frontPreviewImg');
                    const idBackImg = document.querySelector('#backPreviewImg');
                    const selfieImg = document.querySelector('#selfieImg');
                    
                    // Check if all images are uploaded
                    if (!idFrontImg || !idFrontImg.src || !idBackImg || !idBackImg.src || !selfieImg || !selfieImg.src) {
                        throw new Error('Please upload all required images (ID front, ID back, and selfie)');
                    }
                    
                    // Wait for all images to fully load before processing
                    console.log('Waiting for images to load...');
                    await Promise.all([
                        new Promise((resolve, reject) => {
                            if (idFrontImg.complete) resolve();
                            else {
                                idFrontImg.onload = resolve;
                                idFrontImg.onerror = reject;
                            }
                        }),
                        new Promise((resolve, reject) => {
                            if (idBackImg.complete) resolve();
                            else {
                                idBackImg.onload = resolve;
                                idBackImg.onerror = reject;
                            }
                        }),
                        new Promise((resolve, reject) => {
                            if (selfieImg.complete) resolve();
                            else {
                                selfieImg.onload = resolve;
                                selfieImg.onerror = reject;
                            }
                        })
                    ]);
                    console.log('All images loaded, starting verification...');
                    
                    // Run verification
                    const results = await aiVerification.verifyImages(idFrontImg, idBackImg, selfieImg);
                    
                    // Store results
                    this.aiVerificationData = results;
                    
                    // Update hidden input
                    const hiddenInput = document.getElementById('ai_verification_data');
                    if (hiddenInput) {
                        hiddenInput.value = JSON.stringify(results);
                    }
                    
                    // Return true to continue (main flow will handle the status)
                    return true;
                    
                } catch (error) {
                    console.error('AI Verification Error:', error);
                    
                    // Store error status
                    this.aiVerificationData = {
                        error: error.message,
                        status: 'failed',
                        notes: ['AI verification system error - requires manual review']
                    };
                    
                    // Update hidden input
                    const hiddenInput = document.getElementById('ai_verification_data');
                    if (hiddenInput) {
                        hiddenInput.value = JSON.stringify(this.aiVerificationData);
                    }
                    
                    // Return true - let main flow handle it
                    return true;
                } finally {
                    this.isVerifying = false;
                }
            },
            
            async submitRegistration() {
                // Show loading
                Swal.fire({
                    title: 'Verifying your ID with Staff...',
                    text: 'Please wait while our staff verifies your documents',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Run AI verification (with fallback to manual review if it fails)
                const verified = await this.verifyIDsWithAI();
                
                Swal.close();
                
                // If AI verification completely failed, allow registration but mark for manual review
                if (!verified && this.aiVerificationData.status === 'failed') {
                    await Swal.fire({
                        icon: 'info',
                        title: 'Manual Review Required',
                        html: `
                            <p>Our AI verification system is currently unavailable.</p>
                            <p><strong>Your registration will proceed, but your ID will be manually reviewed by our staff.</strong></p>
                            <p>You'll be notified once approved (usually within 24 hours).</p>
                        `,
                        confirmButtonColor: '#00473e',
                        confirmButtonText: 'Proceed with Registration'
                    });
                    
                    // Set status to manual_review so it doesn't auto-approve
                    this.aiVerificationData.status = 'manual_review';
                    const hiddenInput = document.getElementById('ai_verification_data');
                    if (hiddenInput) {
                        hiddenInput.value = JSON.stringify(this.aiVerificationData);
                    }
                }
                
                // Show success message
                if (this.aiVerificationData.status === 'passed') {
                    Swal.fire({
                        icon: 'success',
                        title: 'ID Verified!',
                        text: 'Your ID has been verified successfully',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else if (this.aiVerificationData.status === 'manual_review') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Under Review',
                        text: 'Your ID is being reviewed by our team. You\'ll be notified once approved.',
                        confirmButtonColor: '#00473e'
                    });
                }
                
                // Wait a moment for the user to see the message
                await new Promise(resolve => setTimeout(resolve, 2000));
                
                // Submit the form
                document.querySelector('form[action="{{ route('register') }}"]').submit();
            },
            
            async resendVerificationEmail() {
                try {
                    Swal.fire({
                        title: 'Resending Email...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    const response = await fetch('{{ route('register.resend-email') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    let data;
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        data = await response.json();
                    } else {
                        const text = await response.text();
                        console.error('Non-JSON response:', text);
                        throw new Error('Server returned invalid response');
                    }
                    
                    Swal.close();
                    
                    if (response.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Email Sent!',
                            text: 'A new verification code has been sent to your email.',
                            confirmButtonColor: '#00473e'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: data.message || 'Failed to resend email. Please try again.',
                            confirmButtonColor: '#00473e'
                        });
                    }
                } catch (error) {
                    console.error('Resend error:', error);
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'An error occurred. Please try again.',
                        confirmButtonColor: '#00473e'
                    });
                }
            }
        }
    }

    // ==================== PHILIPPINE ADDRESS CASCADING DROPDOWNS ====================
    document.addEventListener('DOMContentLoaded', function() {
        const regionSelect = document.getElementById('region_id');
        const provinceSelect = document.getElementById('province_id');
        const citySelect = document.getElementById('city_id');
        const barangaySelect = document.getElementById('barangay_id');
        const districtContainer = document.getElementById('district_container');

        // Load all regions on page load
        if (regionSelect) {
            fetch('/api/get-regions')
                .then(response => response.json())
                .then(regions => {
                    regionSelect.innerHTML = '<option value="">Select Region</option>';
                    regions.forEach(region => {
                        const option = document.createElement('option');
                        option.value = region.id;
                        option.textContent = region.long_name;
                        option.dataset.code = region.code;
                        regionSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading regions:', error);
                    regionSelect.innerHTML = '<option value="">Error loading regions</option>';
                });
        }

        // Load provinces when region changes
        if (regionSelect && provinceSelect) {
            regionSelect.addEventListener('change', function() {
                const regionId = this.value;
                
                // Reset dependent dropdowns
                provinceSelect.innerHTML = '<option value="">Loading...</option>';
                provinceSelect.disabled = false;
                citySelect.innerHTML = '<option value="">Select Province First</option>';
                citySelect.disabled = true;
                barangaySelect.innerHTML = '<option value="">Select City/Municipality First</option>';
                barangaySelect.disabled = true;
                
                if (regionId) {
                    fetch(`/api/get-provinces/${regionId}`)
                        .then(response => response.json())
                        .then(provinces => {
                            provinceSelect.innerHTML = '<option value="">Select Province</option>';
                            provinces.forEach(province => {
                                const option = document.createElement('option');
                                option.value = province.id;
                                option.textContent = province.name;
                                provinceSelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error loading provinces:', error);
                            provinceSelect.innerHTML = '<option value="">Error loading provinces</option>';
                        });
                } else {
                    provinceSelect.innerHTML = '<option value="">Select Region First</option>';
                    provinceSelect.disabled = true;
                }
            });
        }

        // Load cities when province changes
        if (provinceSelect && citySelect) {
            provinceSelect.addEventListener('change', function() {
                const provinceId = this.value;
                
                // Reset dependent dropdowns
                citySelect.innerHTML = '<option value="">Loading...</option>';
                citySelect.disabled = false;
                barangaySelect.innerHTML = '<option value="">Select City/Municipality First</option>';
                barangaySelect.disabled = true;
                
                if (provinceId) {
                    fetch(`/api/get-cities/${provinceId}`)
                        .then(response => response.json())
                        .then(cities => {
                            citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
                            cities.forEach(city => {
                                const option = document.createElement('option');
                                option.value = city.id;
                                option.textContent = city.name + (city.type === 'city' ? ' (City)' : ' (Municipality)');
                                option.dataset.code = city.code;
                                option.dataset.zipCode = city.zip_code || ''; // Store zip code in dataset
                                citySelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error loading cities:', error);
                            citySelect.innerHTML = '<option value="">Error loading cities</option>';
                        });
                } else {
                    citySelect.innerHTML = '<option value="">Select Province First</option>';
                    citySelect.disabled = true;
                }
            });
        }

        // Load districts when city changes (NO ZIP CODE AUTO-FILL HERE)
        if (citySelect) {
            const districtSelect = document.getElementById('district_id');
            const zipCodeInput = document.getElementById('zip_code');
            
            citySelect.addEventListener('change', function() {
                const cityId = this.value;
                
                // Clear zip code when city changes (will be filled when barangay is selected)
                if (zipCodeInput) {
                    zipCodeInput.value = '';
                    zipCodeInput.style.backgroundColor = '';
                }
                
                // Reset district and barangay dropdowns
                if (districtSelect) {
                    districtSelect.innerHTML = '<option value="">Loading...</option>';
                    districtSelect.disabled = false;
                }
                if (barangaySelect) {
                    barangaySelect.innerHTML = '<option value="">Select District First</option>';
                    barangaySelect.disabled = true;
                }
                
                // Load districts for the selected city
                if (cityId) {
                    fetch(`/api/get-districts/${cityId}`)
                        .then(response => response.json())
                        .then(districts => {
                            districtSelect.innerHTML = '<option value="">Select District</option>';
                            districts.forEach(district => {
                                const option = document.createElement('option');
                                option.value = district.id;
                                option.textContent = district.name;
                                districtSelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error loading districts:', error);
                            districtSelect.innerHTML = '<option value="">Error loading districts</option>';
                        });
                } else {
                    if (districtSelect) {
                        districtSelect.innerHTML = '<option value="">Select City First</option>';
                        districtSelect.disabled = true;
                    }
                }
            });
        }

        // Load barangays when district changes (NO ZIP CODE AUTO-FILL HERE)
        const districtSelect = document.getElementById('district_id');
        if (districtSelect && barangaySelect) {
            districtSelect.addEventListener('change', function() {
                const districtId = this.value;
                const zipCodeInput = document.getElementById('zip_code');
                
                // Clear zip code when district changes (will be filled when barangay is selected)
                if (zipCodeInput) {
                    zipCodeInput.value = '';
                    zipCodeInput.style.backgroundColor = '';
                }
                
                // Reset barangay dropdown
                barangaySelect.innerHTML = '<option value="">Loading...</option>';
                barangaySelect.disabled = false;
                
                if (districtId) {
                    fetch(`/api/get-barangays-by-district/${districtId}`)
                        .then(response => response.json())
                        .then(barangays => {
                            barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
                            barangays.forEach(barangay => {
                                const option = document.createElement('option');
                                option.value = barangay.id;
                                option.textContent = barangay.name;
                                if (barangay.alternate_name) {
                                    option.textContent += ' (' + barangay.alternate_name + ')';
                                }
                                // Store zip code in option dataset
                                if (barangay.zip_code) {
                                    option.dataset.zipCode = barangay.zip_code;
                                }
                                barangaySelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error loading barangays:', error);
                            barangaySelect.innerHTML = '<option value="">Error loading barangays</option>';
                        });
                } else {
                    barangaySelect.innerHTML = '<option value="">Select District First</option>';
                    barangaySelect.disabled = true;
                }
            });
        }

        // Auto-fill ZIP CODE when barangay is selected
        if (barangaySelect) {
            barangaySelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const zipCode = selectedOption.dataset.zipCode;
                const zipCodeInput = document.getElementById('zip_code');
                
                if (zipCodeInput && zipCode) {
                    zipCodeInput.value = zipCode;
                    zipCodeInput.style.backgroundColor = '#e8f5e9'; // Light green to indicate auto-filled
                } else if (zipCodeInput) {
                    zipCodeInput.value = '';
                    zipCodeInput.style.backgroundColor = '';
                }
            });
        }

        // Allow users to manually edit the zip code (remove auto-fill styling)
        const zipCodeInput = document.getElementById('zip_code');
        if (zipCodeInput) {
            zipCodeInput.addEventListener('input', function() {
                this.style.backgroundColor = '';
            });
        }

        // Image Preview Functionality
        // Front ID image preview
        const frontFileInput = document.getElementById('valid_id_front_image');
        const frontImagePreview = document.getElementById('frontImagePreview');
        const frontPreviewImg = document.getElementById('frontPreviewImg');
        const removeFrontImageBtn = document.getElementById('removeFrontImage');

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

        if (removeFrontImageBtn) {
            removeFrontImageBtn.addEventListener('click', function() {
                frontFileInput.value = '';
                frontImagePreview.style.display = 'none';
                frontPreviewImg.src = '';
            });
        }

        // Back ID image preview
        const backFileInput = document.getElementById('valid_id_back_image');
        const backImagePreview = document.getElementById('backImagePreview');
        const backPreviewImg = document.getElementById('backPreviewImg');
        const removeBackImageBtn = document.getElementById('removeBackImage');

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

        if (removeBackImageBtn) {
            removeBackImageBtn.addEventListener('click', function() {
                backFileInput.value = '';
                backImagePreview.style.display = 'none';
                backPreviewImg.src = '';
            });
        }

        // Selfie image preview
        const selfieInput = document.getElementById('selfie_with_id_image');
        const selfiePreview = document.getElementById('selfiePreview');
        const selfieImg = document.getElementById('selfieImg');
        const removeSelfieBtn = document.getElementById('removeSelfie');

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

        if (removeSelfieBtn) {
            removeSelfieBtn.addEventListener('click', function() {
                selfieInput.value = '';
                selfiePreview.style.display = 'none';
                selfieImg.src = '';
            });
        }
    });
</script>
@endpush
@endsection


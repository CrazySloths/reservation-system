<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user_id']) || !in_array('citizen', $_SESSION['roles'] ?? [])) {
    header('Location: ../public/login.php');
    exit;
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

include 'header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6">
                <div class="hero-content fade-in">
                    <h1 class="hero-title display-3 fw-bold mb-4">Your Gateway to <span class="text-highlight">Government Services</span></h1>
                    <p class="hero-subtitle lead mb-4">Experience seamless, transparent, and efficient access to all LGU1 services from the comfort of your home.</p>
                    <div class="hero-buttons">
                        <a href="#services" class="btn btn-primary btn-lg me-3 px-4 py-3">
                            <i class="bi bi-rocket-takeoff me-2"></i>Get Started
                        </a>
                        <a href="#about" class="btn btn-outline-primary btn-lg px-4 py-3">
                            <i class="bi bi-play-circle me-2"></i>Learn More
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image floating">
                    <div class="hero-card">
                        <i class="bi bi-building display-1 mb-3" style="color: <?= $colors['highlight'] ?>;"></i>
                        <h3 class="fw-bold" style="color: <?= $colors['headline'] ?>;">LGU1 Digital Services</h3>
                        <p class="text-muted">Modernizing government services for better citizen experience</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section py-5" style="background: <?= $colors['background'] ?>;">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <div class="stat-number display-4 fw-bold" style="color: <?= $colors['highlight'] ?>;">10+</div>
                    <div class="stat-label h5" style="color: <?= $colors['headline'] ?>;">Services Available</div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <div class="stat-number display-4 fw-bold" style="color: <?= $colors['highlight'] ?>;">24/7</div>
                    <div class="stat-label h5" style="color: <?= $colors['headline'] ?>;">Online Access</div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <div class="stat-number display-4 fw-bold" style="color: <?= $colors['highlight'] ?>;">1000+</div>
                    <div class="stat-label h5" style="color: <?= $colors['headline'] ?>;">Citizens Served</div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <div class="stat-number display-4 fw-bold" style="color: <?= $colors['highlight'] ?>;">99%</div>
                    <div class="stat-label h5" style="color: <?= $colors['headline'] ?>;">Satisfaction Rate</div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container-fluid py-5" id="services">
    <!-- Services Header -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h2 class="display-5 fw-bold mb-4" style="color: <?= $colors['headline'] ?>;">Our Services</h2>
            <p class="lead text-muted mb-5">Explore our comprehensive range of government services designed for your convenience</p>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-4 fade-in" style="animation-delay: 0.1s;">
            <div class="card service-card h-100 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="service-icon">
                        <i class="bi bi-tools display-6 text-white"></i>
                    </div>
                    <h5 class="card-title fw-bold" style="color: <?= $colors['headline'] ?>;">Infrastructure Services</h5>
                    <p class="card-text text-muted mb-4">Track projects, view progress, and access completion reports</p>
                    <a href="#" class="btn btn-outline-primary rounded-pill px-4">Explore <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 fade-in" style="animation-delay: 0.2s;">
            <div class="card service-card h-100 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="service-icon">
                        <i class="bi bi-lightning display-6 text-white"></i>
                    </div>
                    <h5 class="card-title fw-bold" style="color: <?= $colors['headline'] ?>;">Utility Services</h5>
                    <p class="card-text text-muted mb-4">Manage billing, payments, and service connections</p>
                    <a href="#" class="btn btn-outline-primary rounded-pill px-4">Explore <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 fade-in" style="animation-delay: 0.3s;">
            <div class="card service-card h-100 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="service-icon">
                        <i class="bi bi-geo-alt display-6 text-white"></i>
                    </div>
                    <h5 class="card-title fw-bold" style="color: <?= $colors['headline'] ?>;">Transportation</h5>
                    <p class="card-text text-muted mb-4">Road maintenance schedules and damage reporting</p>
                    <a href="#" class="btn btn-outline-primary rounded-pill px-4">Explore <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 fade-in" style="animation-delay: 0.4s;">
            <div class="card service-card h-100 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="service-icon">
                        <i class="bi bi-building display-6 text-white"></i>
                    </div>
                    <h5 class="card-title fw-bold" style="color: <?= $colors['headline'] ?>;">Public Facilities</h5>
                    <p class="card-text text-muted mb-4">Reserve facilities and manage bookings online</p>
                    <a href="#" class="btn btn-outline-primary rounded-pill px-4">Explore <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 fade-in" style="animation-delay: 0.5s;">
            <div class="card service-card h-100 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="service-icon">
                        <i class="bi bi-house-heart display-6 text-white"></i>
                    </div>
                    <h5 class="card-title fw-bold" style="color: <?= $colors['headline'] ?>;">Housing Services</h5>
                    <p class="card-text text-muted mb-4">Housing applications and resettlement programs</p>
                    <a href="#" class="btn btn-outline-primary rounded-pill px-4">Explore <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 fade-in" style="animation-delay: 0.6s;">
            <div class="card service-card h-100 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="service-icon">
                        <i class="bi bi-leaf display-6 text-white"></i>
                    </div>
                    <h5 class="card-title fw-bold" style="color: <?= $colors['headline'] ?>;">Energy Programs</h5>
                    <p class="card-text text-muted mb-4">Renewable energy and efficiency initiatives</p>
                    <a href="#" class="btn btn-outline-primary rounded-pill px-4">Explore <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header" style="background: <?= $colors['background'] ?>; border-bottom: 2px solid <?= $colors['highlight'] ?>;">
                    <h4 class="mb-0" style="color: <?= $colors['headline'] ?>;">
                        <i class="bi bi-lightning-charge"></i> Quick Actions
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <a href="#" class="btn quick-action-btn btn-lg w-100" style="background: <?= $colors['button'] ?>; color: <?= $colors['button_text'] ?>;">
                                <i class="bi bi-file-earmark-text d-block mb-2 display-6"></i>
                                <strong>Apply for Services</strong>
                                <small class="d-block mt-1">Start new application</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="#" class="btn btn-outline-secondary quick-action-btn btn-lg w-100">
                                <i class="bi bi-search d-block mb-2 display-6"></i>
                                <strong>Track Application</strong>
                                <small class="d-block mt-1">Check status</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="#" class="btn btn-outline-secondary quick-action-btn btn-lg w-100">
                                <i class="bi bi-calendar-check d-block mb-2 display-6"></i>
                                <strong>Book Appointment</strong>
                                <small class="d-block mt-1">Schedule visit</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="#" class="btn btn-outline-secondary quick-action-btn btn-lg w-100">
                                <i class="bi bi-telephone d-block mb-2 display-6"></i>
                                <strong>Contact Support</strong>
                                <small class="d-block mt-1">Get help</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements -->
    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header" style="background: <?= $colors['background'] ?>; border-bottom: 2px solid <?= $colors['highlight'] ?>;">
                    <h5 class="mb-0" style="color: <?= $colors['headline'] ?>;">
                        <i class="bi bi-megaphone"></i> Latest Announcements
                    </h5>
                </div>
                <div class="card-body">
                    <div class="announcement-item">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-megaphone-fill me-3 mt-1" style="color: <?= $colors['highlight'] ?>;"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold" style="color: <?= $colors['headline'] ?>;">New Online Services Available</h6>
                                <p class="mb-2 text-muted">Citizens can now access more services through the online portal with enhanced features.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><i class="bi bi-clock me-1"></i>2 days ago</small>
                                    <span class="badge bg-success">New</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="announcement-item">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-tools me-3 mt-1" style="color: <?= $colors['tertiary'] ?>;"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold" style="color: <?= $colors['headline'] ?>;">Scheduled Maintenance Notice</h6>
                                <p class="mb-2 text-muted">System maintenance scheduled for this weekend. Services may be temporarily unavailable.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><i class="bi bi-clock me-1"></i>1 week ago</small>
                                    <span class="badge bg-warning">Important</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card info-card border-0 shadow-lg h-100">
                <div class="card-header border-0 text-center py-4">
                    <i class="bi bi-info-circle display-4 mb-3"></i>
                    <h5 class="mb-0 text-white fw-bold">Quick Info</h5>
                </div>
                <div class="card-body text-white">
                    <div class="mb-4 p-3 rounded" style="background: rgba(255,255,255,0.1);">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-clock me-2"></i>
                            <strong>Office Hours</strong>
                        </div>
                        <span>Monday - Friday<br>8:00 AM - 5:00 PM</span>
                    </div>
                    <div class="mb-4 p-3 rounded" style="background: rgba(255,255,255,0.1);">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-telephone me-2"></i>
                            <strong>Emergency Hotline</strong>
                        </div>
                        <span>911 or (02) 123-4567</span>
                    </div>
                    <div class="p-3 rounded" style="background: rgba(255,255,255,0.1);">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-envelope me-2"></i>
                            <strong>Email Support</strong>
                        </div>
                        <span>support@lgu1.gov.ph</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- About Section -->
<section class="about-section py-5" id="about" style="background: linear-gradient(135deg, <?= $colors['headline'] ?> 0%, <?= $colors['paragraph'] ?> 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 text-white">
                <h2 class="display-5 fw-bold mb-4">About LGU1 Digital Portal</h2>
                <p class="lead mb-4">We are committed to providing efficient, transparent, and accessible government services to all citizens of LGU1.</p>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-3" style="color: <?= $colors['highlight'] ?>; font-size: 1.5rem;"></i>
                            <span>Secure Transactions</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-3" style="color: <?= $colors['highlight'] ?>; font-size: 1.5rem;"></i>
                            <span>Fast Processing</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-3" style="color: <?= $colors['highlight'] ?>; font-size: 1.5rem;"></i>
                            <span>24/7 Availability</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-3" style="color: <?= $colors['highlight'] ?>; font-size: 1.5rem;"></i>
                            <span>Mobile Friendly</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-image text-center">
                    <div class="about-card p-5 rounded-3" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
                        <i class="bi bi-award display-1 mb-3" style="color: <?= $colors['highlight'] ?>;"></i>
                        <h4 class="text-white mb-3">Excellence in Service</h4>
                        <p class="text-light">Recognized for outstanding digital transformation in local government services</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold mb-4" style="color: <?= $colors['headline'] ?>;">Need Help?</h2>
                <p class="lead mb-5 text-muted">Our support team is here to assist you with any questions or concerns.</p>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="contact-item p-4 rounded-3 h-100" style="background: <?= $colors['background'] ?>;">
                            <i class="bi bi-telephone display-4 mb-3" style="color: <?= $colors['highlight'] ?>;"></i>
                            <h5 style="color: <?= $colors['headline'] ?>;">Call Us</h5>
                            <p class="text-muted mb-0">(02) 123-4567</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="contact-item p-4 rounded-3 h-100" style="background: <?= $colors['background'] ?>;">
                            <i class="bi bi-envelope display-4 mb-3" style="color: <?= $colors['highlight'] ?>;"></i>
                            <h5 style="color: <?= $colors['headline'] ?>;">Email Us</h5>
                            <p class="text-muted mb-0">support@lgu1.gov.ph</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="contact-item p-4 rounded-3 h-100" style="background: <?= $colors['background'] ?>;">
                            <i class="bi bi-geo-alt display-4 mb-3" style="color: <?= $colors['highlight'] ?>;"></i>
                            <h5 style="color: <?= $colors['headline'] ?>;">Visit Us</h5>
                            <p class="text-muted mb-0">LGU1 Building, Main St.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer-section py-5" style="background: <?= $colors['headline'] ?>; color: white;">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="d-flex align-items-center mb-3">
                    <img src="../assets/images/logo.png" alt="LGU1" style="height: 50px;" class="me-3">
                    <h4 class="mb-0">LGU1</h4>
                </div>
                <p class="text-light mb-3">Serving our community with excellence, transparency, and innovation in government services.</p>
                <div class="social-links">
                    <a href="#" class="text-light me-3"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-light me-3"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="text-light me-3"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-light"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="fw-bold mb-3" style="color: <?= $colors['highlight'] ?>;">Services</h6>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-light text-decoration-none">Infrastructure</a></li>
                    <li><a href="#" class="text-light text-decoration-none">Utilities</a></li>
                    <li><a href="#" class="text-light text-decoration-none">Housing</a></li>
                    <li><a href="#" class="text-light text-decoration-none">Transportation</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="fw-bold mb-3" style="color: <?= $colors['highlight'] ?>;">Quick Links</h6>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-light text-decoration-none">About Us</a></li>
                    <li><a href="#" class="text-light text-decoration-none">Contact</a></li>
                    <li><a href="#" class="text-light text-decoration-none">Privacy Policy</a></li>
                    <li><a href="#" class="text-light text-decoration-none">Terms of Service</a></li>
                </ul>
            </div>
            <div class="col-lg-4 mb-4">
                <h6 class="fw-bold mb-3" style="color: <?= $colors['highlight'] ?>;">Contact Info</h6>
                <div class="mb-2">
                    <i class="bi bi-geo-alt me-2" style="color: <?= $colors['highlight'] ?>;"></i>
                    <span>LGU1 Building, Main Street, City</span>
                </div>
                <div class="mb-2">
                    <i class="bi bi-telephone me-2" style="color: <?= $colors['highlight'] ?>;"></i>
                    <span>(02) 123-4567</span>
                </div>
                <div class="mb-2">
                    <i class="bi bi-envelope me-2" style="color: <?= $colors['highlight'] ?>;"></i>
                    <span>info@lgu1.gov.ph</span>
                </div>
                <div>
                    <i class="bi bi-clock me-2" style="color: <?= $colors['highlight'] ?>;"></i>
                    <span>Mon-Fri: 8:00 AM - 5:00 PM</span>
                </div>
            </div>
        </div>
        <hr class="my-4" style="border-color: <?= $colors['highlight'] ?>33;">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0 text-light">&copy; 2024 Local Government Unit 1. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <small class="text-light">Powered by LGU1 Digital Services</small>
            </div>
        </div>
    </div>
</footer>

<style>
.hero-section {
    background: linear-gradient(rgba(0,71,62,0.8), rgba(71,93,91,0.8)), url('../assets/images/background.png');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    right: -50%;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, transparent 40%, <?= $colors['highlight'] ?>10 50%, transparent 60%);
    transform: rotate(-15deg);
}

.hero-title {
    color: white;
    line-height: 1.2;
}

.text-highlight {
    color: <?= $colors['highlight'] ?>;
    position: relative;
}

.hero-subtitle {
    color: white;
    font-size: 1.2rem;
}

.hero-card {
    background: white;
    padding: 3rem;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,71,62,0.1);
    text-align: center;
    border: 1px solid <?= $colors['highlight'] ?>20;
}

.stat-item {
    padding: 2rem 1rem;
    transition: transform 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-5px);
}

.contact-item {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.contact-item:hover {
    transform: translateY(-5px);
    border-color: <?= $colors['highlight'] ?>;
    box-shadow: 0 10px 30px rgba(0,71,62,0.1);
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-buttons .btn {
        display: block;
        width: 100%;
        margin-bottom: 1rem;
    }
}

.hero-stats {
    background: rgba(255,255,255,0.1);
    border-radius: 15px;
    padding: 20px;
    backdrop-filter: blur(10px);
}

.service-card {
    border: none;
    border-radius: 15px;
    transition: all 0.3s ease;
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
    position: relative;
    overflow: hidden;
}

.service-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(250,174,43,0.1), transparent);
    transition: left 0.6s;
}

.service-card:hover::before {
    left: 100%;
}

.service-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,71,62,0.15);
}

.service-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, <?= $colors['highlight'] ?> 0%, <?= $colors['secondary'] ?> 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    transition: transform 0.3s ease;
}

.service-card:hover .service-icon {
    transform: scale(1.1) rotate(5deg);
}

.quick-action-btn {
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
}

.quick-action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.quick-action-btn:hover::before {
    left: 100%;
}

.quick-action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.announcement-item {
    border-left: 4px solid <?= $colors['highlight'] ?>;
    padding: 15px;
    margin-bottom: 15px;
    background: linear-gradient(135deg, #ffffff 0%, <?= $colors['background'] ?> 100%);
    border-radius: 8px;
    transition: all 0.3s ease;
}

.announcement-item:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.info-card {
    background: linear-gradient(135deg, <?= $colors['headline'] ?> 0%, <?= $colors['paragraph'] ?> 100%);
    color: white;
    border-radius: 15px;
}

.pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.fade-in {
    animation: fadeIn 0.8s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.slide-in-left {
    animation: slideInLeft 0.6s ease-out;
}

@keyframes slideInLeft {
    from { opacity: 0; transform: translateX(-30px); }
    to { opacity: 1; transform: translateX(0); }
}

.slide-in-right {
    animation: slideInRight 0.6s ease-out;
}

@keyframes slideInRight {
    from { opacity: 0; transform: translateX(30px); }
    to { opacity: 1; transform: translateX(0); }
}

.floating {
    animation: floating 3s ease-in-out infinite;
}

@keyframes floating {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}
.btn-outline-primary {
    border-color: <?= $colors['highlight'] ?>;
    color: <?= $colors['highlight'] ?>;
}
.btn-outline-primary:hover {
    background: <?= $colors['highlight'] ?>;
    color: <?= $colors['button_text'] ?>;
}
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}
</style>

</body>
</html>
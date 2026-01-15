<?php
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LGU1 Citizen Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .navbar {
            background: linear-gradient(90deg, <?= $colors['headline'] ?> 0%, <?= $colors['paragraph'] ?> 100%);
            box-shadow: 0 2px 8px rgba(0,71,62,0.15);
        }
        .navbar-brand img {
            height: 40px;
        }
        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 500;
            transition: color 0.3s;
        }
        .navbar-nav .nav-link:hover {
            color: <?= $colors['highlight'] ?> !important;
        }
        .dropdown-menu {
            background: white;
            border: 1px solid <?= $colors['highlight'] ?>;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0,71,62,0.1);
        }
        .dropdown-item {
            color: <?= $colors['headline'] ?>;
            padding: 8px 16px;
            transition: background 0.3s;
        }
        .dropdown-item:hover {
            background: <?= $colors['background'] ?>;
            color: <?= $colors['headline'] ?>;
        }
        .navbar-toggler {
            border: 1px solid <?= $colors['highlight'] ?>;
        }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23faae2b' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
    </style>
</head>
<body style="background-color: <?= $colors['background'] ?>;">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="home.php">
                <img src="../assets/images/logo.png" alt="LGU1 Logo" class="me-2">
                <span class="text-white fw-bold">LGU1 Citizen Portal</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php"><i class="bi bi-house"></i> Home</a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-tools"></i> Infrastructure
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-building"></i> Project Planning</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-graph-up"></i> Progress Tracking</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-clipboard-check"></i> Completion Reports</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-lightning"></i> Utilities
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-receipt"></i> Billing & Invoicing</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-credit-card"></i> Payment Tracking</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-telephone"></i> Service Requests</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-geo-alt"></i> Transportation
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-calendar-check"></i> Road Maintenance</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-exclamation-triangle"></i> Damage Reports</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-bar-chart"></i> Traffic Monitoring</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-building"></i> Facilities
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-calendar"></i> Online Booking</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-cash"></i> Fee Calculation</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-star"></i> Usage Reports</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-house-heart"></i> Housing
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person-check"></i> Eligibility Check</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-key"></i> Unit Assignment</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-cash-coin"></i> Loan Tracking</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> Account
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="../public/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
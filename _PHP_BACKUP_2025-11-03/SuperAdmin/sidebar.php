<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';

// Validate session
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || !isset($_SESSION['session_token'])) {
    session_destroy();
    header('Location: ../public/login.php');
    exit;
}

// Check session timeout (30 minutes)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_destroy();
    header('Location: ../public/login.php?timeout=1');
    exit;
}

// Update last activity
$_SESSION['last_activity'] = time();

$user_role = $_SESSION['user_role'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

// Super Admin menu items based on subsystems
$menu_items = [
    'super admin' => [
        'main' => [
            ['icon' => 'dashboard', 'label' => 'Dashboard', 'href' => 'dashboard.php', 'priority' => 'high'],
            ['icon' => 'users', 'label' => 'User Management', 'href' => 'users.php', 'priority' => 'high'],
            ['icon' => 'shield', 'label' => 'Role Management', 'href' => 'roleManagement.php', 'priority' => 'high'],
            ['icon' => 'reports', 'label' => 'System Reports', 'href' => 'reports.php', 'priority' => 'high']
        ],
        'subsystems' => [
            ['icon' => 'building', 'label' => 'Infrastructure Project Management', 'href' => 'https://pm.local-government-unit-1-ph.com/dashboard.php'],
            ['icon' => 'utilities', 'label' => 'Utility Billing & Monitoring', 'href' => 'https://billing.local-government-unit-1-ph.com/dashboard.php'],
            ['icon' => 'road', 'label' => 'Road & Transportation Monitoring', 'href' => 'https://road-trans.local-government-unit-1-ph.com/dashboard.php'],
            ['icon' => 'calendar', 'label' => 'Public Facilities Reservation', 'href' => 'https://facilities.local-government-unit-1-ph.com/dashboard.php'],
            ['icon' => 'maintenance', 'label' => 'Community Infrastructure Maintenance', 'href' => 'https://community.local-government-unit-1-ph.com/dashboard.php'],
            ['icon' => 'planning', 'label' => 'Urban Planning & Development', 'href' => 'https://planning.local-government-unit-1-ph.com/dashboard.php'],
            ['icon' => 'land', 'label' => 'Land Registration & Titling', 'href' => 'https://lang-reg.local-government-unit-1-ph.com/dashboard.php'],
            ['icon' => 'home', 'label' => 'Housing & Resettlement Management', 'href' => 'https://housing.local-government-unit-1-ph.com/dashboard.php'],
            ['icon' => 'energy', 'label' => 'Renewable Energy Projects', 'href' => 'https://renew-energy.local-government-unit-1-ph.com/dashboard.php'],
            ['icon' => 'efficiency', 'label' => 'Energy Efficiency & Conservation', 'href' => 'https://energy.local-government-unit-1-ph.com/dashboard.php']
        ]
    ]
];

$icons = [
    'dashboard' => '<path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>',
    'users' => '<path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>',
    'shield' => '<path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>',
    'reports' => '<path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6z" clip-rule="evenodd"/>',
    'building' => '<path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2V4z" clip-rule="evenodd"/><path d="M6 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H8a2 2 0 01-2-2V4z"/>',
    'utilities' => '<path fill-rule="evenodd" d="M5.05 3.636a1 1 0 010 1.414 7 7 0 000 9.9 1 1 0 11-1.414 1.414 9 9 0 010-12.728 1 1 0 011.414 0z" clip-rule="evenodd"/>',
    'road' => '<path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3z"/>',
    'calendar' => '<path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1z" clip-rule="evenodd"/>',
    'maintenance' => '<path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>',
    'planning' => '<path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3h4v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd"/>',
    'land' => '<path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>',
    'home' => '<path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>',
    'energy' => '<path d="M11 3a1 1 0 10-2 0v1a1 1 0 10-2 0V3a3 3 0 116 0v1a1 1 0 10-2 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 000 1.414l.707.707a1 1 0 001.414-1.414L15.657 5.757zM6.464 4.343a1 1 0 00-1.414 1.414l.707.707a1 1 0 001.414 0L6.464 4.343z"/>',
    'efficiency' => '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
];
?>

<div id="admin-sidebar" class="position-fixed top-0 start-0 h-100 shadow-lg d-flex flex-column" style="width: 280px; z-index: 1050; transition: transform 0.3s ease; background: linear-gradient(135deg, #00473e 0%, #475d5b 100%);" role="navigation" aria-label="Main navigation">
    <!-- Sidebar Header -->
    <div class="d-flex align-items-center justify-content-between p-4 border-bottom" style="border-color: rgba(255,255,255,0.1) !important;">
        <div class="d-flex align-items-center">
            <div class="rounded-circle overflow-hidden border border-2 me-3" style="width: 48px; height: 48px; border-color: #faae2b !important; background: rgba(255,255,255,0.1);">
                <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                    <span class="fw-bold" style="color: #faae2b; font-size: 18px;">🏛️</span>
                </div>
            </div>
            <div>
                <h2 class="text-white fw-bold mb-0" style="font-size: 1rem;">LGU1 Super Admin</h2>
                <p class="text-white-50 small mb-0">System Management</p>
            </div>
        </div>
        <button id="sidebar-close" class="btn btn-link text-white d-lg-none p-0" aria-label="Close sidebar">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- User Profile Section -->
    <div class="p-3 border-bottom" style="border-color: rgba(255,255,255,0.1) !important;">
        <div class="d-flex align-items-center">
            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: #faae2b;">
                <svg width="28" height="28" style="color: #00473e;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="text-white fw-semibold small mb-0"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></h3>
                <p class="text-white-50 small mb-1"><?= htmlspecialchars($user_role) ?></p>

            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-fill overflow-auto py-3" role="menubar">
        <?php 
        // Debug: Show current user role
        echo "<!-- Debug: User role is: '" . $user_role . "' -->";
        echo "<!-- Debug: Available roles: " . implode(', ', array_keys($menu_items)) . " -->";
        ?>
        <?php if (isset($menu_items[$user_role]) || in_array($user_role, ['super admin', 'Super Admin'])): ?>
        <?php $current_menu = $menu_items[$user_role] ?? $menu_items['super admin'] ?? []; ?>
            <!-- Main Section -->
            <?php if (isset($current_menu['main'])): ?>
            <div class="px-3 mb-4">
                <h4 class="text-white-50 small fw-semibold text-uppercase mb-3" style="letter-spacing: 0.05em; font-size: 0.75rem;">System Control</h4>
                <ul class="list-unstyled mb-0">
                    <?php foreach ($current_menu['main'] as $item): ?>
                    <li class="mb-1">
                        <a href="<?= $item['href'] ?>" class="sidebar-link d-flex align-items-center px-3 py-2 text-decoration-none rounded" role="menuitem" tabindex="0">
                            <svg width="20" height="20" class="me-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <?= $icons[$item['icon']] ?>
                            </svg>
                            <span class="sidebar-text"><?= $item['label'] ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Subsystems Section -->
            <?php if (isset($current_menu['subsystems'])): ?>
            <div class="px-3 mb-4">
                <h4 class="text-white-50 small fw-semibold text-uppercase mb-3" style="letter-spacing: 0.05em; font-size: 0.75rem;">LGU Subsystems</h4>
                <ul class="list-unstyled mb-0">
                    <?php foreach ($current_menu['subsystems'] as $item): ?>
                    <li class="mb-1">
                        <a href="<?= $item['href'] ?>" class="sidebar-link d-flex align-items-center px-3 py-2 text-decoration-none rounded" role="menuitem" tabindex="0" target="_blank">
                            <svg width="20" height="20" class="me-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <?= $icons[$item['icon']] ?>
                            </svg>
                            <span class="sidebar-text"><?= $item['label'] ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </nav>

    <!-- Sidebar Footer -->
    <div class="p-3 border-top flex-shrink-0" style="border-color: rgba(255,255,255,0.1) !important;">
        <div class="d-grid gap-2">

            <a href="../public/logout.php" class="btn btn-link text-start text-white-50 p-2 d-flex align-items-center text-decoration-none">
                <svg width="16" height="16" class="me-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                </svg>
                Logout
            </a>
        </div>
    </div>
</div>

<style>
.sidebar-link {
    color: rgba(255,255,255,0.7);
    transition: all 0.3s ease;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 12px;
    margin: 2px 8px;
    padding: 12px 16px;
}

.sidebar-link:hover {
    color: #FFFFFF !important;
    background: rgba(255,255,255,0.1) !important;
    transform: translateY(-1px);
}

.sidebar-link.active {
    color: #faae2b !important;
    background: rgba(250,174,43,0.15) !important;
    border: 1px solid rgba(250,174,43,0.3);
    font-weight: 600;
}

@media (max-width: 991.98px) {
    #admin-sidebar {
        transform: translateX(-100%) !important;
    }
    
    #admin-sidebar.show-mobile {
        transform: translateX(0) !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarLinks = document.querySelectorAll('.sidebar-link[href]');
    
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            sidebarLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
</script>
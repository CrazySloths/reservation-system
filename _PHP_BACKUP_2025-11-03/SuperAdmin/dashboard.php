<?php
require_once __DIR__ . '/sidebar.php';

// Super Admin dashboard data functions
function getTotalUsers($conn) {
    try {
        $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE status = 'active'");
        return $stmt->fetchColumn() ?: 0;
    } catch (Exception $e) { return 0; }
}

function getTotalSubsystems($conn) {
    try {
        $stmt = $conn->query("SELECT COUNT(*) FROM subsystems");
        return $stmt->fetchColumn() ?: 0;
    } catch (Exception $e) { return 10; }
}

function getSystemHealth($reports_conn) {
    try {
        if (!$reports_conn) return 98;
        $stmt = $reports_conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success FROM transaction_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $result = $stmt->fetch();
        if ($result && $result['total'] > 0) {
            return round(($result['success'] / $result['total']) * 100);
        }
        return 98;
    } catch (Exception $e) { return 98; }
}

function getActiveTransactions($reports_conn) {
    try {
        if (!$reports_conn) return 0;
        $stmt = $reports_conn->query("SELECT COUNT(*) FROM transaction_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)");
        return $stmt->fetchColumn() ?: 0;
    } catch (Exception $e) { return 0; }
}

function getRecentTransactions($reports_conn, $limit = 5) {
    try {
        if (!$reports_conn) return [];
        $stmt = $reports_conn->prepare("SELECT tl.*, s.name as subsystem_name FROM transaction_logs tl LEFT JOIN lgu1_auth_db.subsystems s ON tl.subsystem_id = s.id ORDER BY tl.created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) { return []; }
}

function getSubsystemStats($reports_conn) {
    try {
        if (!$reports_conn) return [];
        $stmt = $reports_conn->query("SELECT s.name, COUNT(tl.id) as transaction_count, AVG(tl.execution_time_ms) as avg_time FROM lgu1_auth_db.subsystems s LEFT JOIN transaction_logs tl ON s.id = tl.subsystem_id AND tl.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) GROUP BY s.id, s.name ORDER BY transaction_count DESC LIMIT 5");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) { return []; }
}

// Get dashboard data
$dashboard_data = [
    'metrics' => [
        ['label' => 'Total Users', 'value' => getTotalUsers($conn), 'icon' => 'users', 'color' => 'lgu-highlight'],
        ['label' => 'Active Subsystems', 'value' => getTotalSubsystems($conn), 'icon' => 'building', 'color' => 'lgu-secondary'],
        ['label' => 'System Health', 'value' => getSystemHealth($reports_conn ?? null) . '%', 'icon' => 'monitor', 'color' => 'green-600'],
        ['label' => 'Active Transactions', 'value' => getActiveTransactions($reports_conn ?? null), 'icon' => 'chart', 'color' => 'lgu-tertiary']
    ]
];

$recent_transactions = getRecentTransactions($reports_conn ?? null, 5);
$subsystem_stats = getSubsystemStats($reports_conn ?? null);

$icons = [
    'users' => '<path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>',
    'building' => '<path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-6a1 1 0 00-1-1H9a1 1 0 00-1 1v6a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>',
    'monitor' => '<path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd"/>',
    'chart' => '<path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>',
    'database' => '<path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z"/><path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z"/><path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z"/>'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - LGU1</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --lgu-bg: #f2f7f5;
            --lgu-headline: #00473e;
            --lgu-paragraph: #475d5b;
            --lgu-highlight: #faae2b;
            --lgu-success: #10b981;
            --lgu-warning: #f59e0b;
            --lgu-danger: #ef4444;
        }
        
        body { 
            background-color: var(--lgu-bg); 
            font-family: 'Inter', system-ui, -apple-system, sans-serif; 
        }
        
        .sidebar-offset { margin-left: 280px; }
        @media (max-width: 991.98px) { .sidebar-offset { margin-left: 0; } }
        
        .hero-section {
            background: linear-gradient(135deg, var(--lgu-headline) 0%, var(--lgu-paragraph) 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .metric-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0,0,0,0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .metric-card:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
            border-color: var(--lgu-highlight);
        }
        
        .metric-icon {
            background: linear-gradient(135deg, var(--lgu-highlight), #fbbf24);
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(250,174,43,0.3);
        }
        
        .activity-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .activity-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .clickable-card:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
            border-color: var(--lgu-highlight) !important;
        }
        
        .clickable-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
        }
        
        .text-lgu-headline { color: var(--lgu-headline) !important; }
        .text-lgu-paragraph { color: var(--lgu-paragraph) !important; }
        .text-lgu-highlight { color: var(--lgu-highlight) !important; }
        
        .bg-lgu-highlight { background-color: var(--lgu-highlight) !important; }
        .bg-lgu-secondary { background-color: #ffa8ba !important; }
        .bg-lgu-tertiary { background-color: #fa5246 !important; }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-lgu-bg">
    <?php include 'sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="sidebar-offset min-vh-100">
        <section class="hero-section py-5 position-relative">
            <div class="container-fluid position-relative">
                <div class="row align-items-center">
                    <div class="col-auto d-flex align-items-center">
                        <button id="mobile-sidebar-toggle" class="btn btn-link d-lg-none text-white p-0 me-3">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <div>
                            <h1 class="display-6 fw-bold mb-2">🏛️ LGU1 Super Admin Dashboard</h1>
                            <p class="lead mb-0 opacity-90">Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>! Monitor all subsystems</p>
                        </div>
                    </div>
                    <div class="col-auto ms-auto d-none d-md-block">
                        <div class="text-end">
                            <p class="small mb-1 opacity-75">Today's Date</p>
                            <p class="h5 fw-semibold mb-0" id="current-date"></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <main class="container-fluid p-4">
            <!-- Key Metrics Cards -->
            <div class="row g-4 mb-4">
                <?php foreach($dashboard_data['metrics'] as $index => $metric): ?>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card metric-card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="metric-icon me-3" style="background: linear-gradient(135deg, <?= $metric['color'] == 'lgu-highlight' ? 'var(--lgu-highlight), #fbbf24' : ($metric['color'] == 'lgu-secondary' ? '#3b82f6, #1d4ed8' : ($metric['color'] == 'green-600' ? 'var(--lgu-success), #059669' : 'var(--lgu-warning), #f59e0b')) ?>);">
                                    <svg width="20" height="20" class="text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <?= isset($icons[$metric['icon']]) ? $icons[$metric['icon']] : $icons['users'] ?>
                                    </svg>
                                </div>
                                <?php if($index === 2): ?>
                                <span class="status-badge bg-success text-white">✓ Live</span>
                                <?php endif; ?>
                            </div>
                            <h6 class="fw-bold text-lgu-headline mb-2"><?= $metric['label'] ?></h6>
                            <p class="h4 fw-bold text-lgu-headline mb-0"><?= $metric['value'] ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- All Subsystem Dashboards -->
            <div class="row g-4 mb-4">
                <?php 
                $auth_params = '?user_id=' . $_SESSION['user_id'] . '&token=' . base64_encode($_SESSION['username']);
                $subsystem_dashboards = [
                    ['name' => 'Infrastructure Project Management', 'url' => 'https://pm.local-government-unit-1-ph.com/dashboard.php' . $auth_params, 'icon' => '🏗️'],
                    ['name' => 'Utility Billing & Monitoring', 'url' => 'https://billing.local-government-unit-1-ph.com/dashboard.php' . $auth_params, 'icon' => '⚡'],
                    ['name' => 'Road & Transportation', 'url' => 'https://road-trans.local-government-unit-1-ph.com/dashboard.php' . $auth_params, 'icon' => '🛣️'],
                    ['name' => 'Public Facilities Reservation', 'url' => 'https://facilities.local-government-unit-1-ph.com/dashboard.php' . $auth_params, 'icon' => '🏢'],
                    ['name' => 'Community Infrastructure', 'url' => 'https://community.local-government-unit-1-ph.com/dashboard.php' . $auth_params, 'icon' => '🏘️'],
                    ['name' => 'Urban Planning & Development', 'url' => 'https://planning.local-government-unit-1-ph.com/dashboard.php' . $auth_params, 'icon' => '🏙️'],
                    ['name' => 'Land Registration & Titling', 'url' => 'https://lang-reg.local-government-unit-1-ph.com/dashboard.php' . $auth_params, 'icon' => '📋'],
                    ['name' => 'Housing & Resettlement', 'url' => 'https://qcitizen-management.local-government-unit-1-ph.com/staff/dashboard.php' . $auth_params, 'icon' => '🏠'],
                    ['name' => 'Renewable Energy Projects', 'url' => 'https://renew-energy.local-government-unit-1-ph.com/dashboard.php' . $auth_params, 'icon' => '🌱'],
                    ['name' => 'Energy Efficiency & Conservation', 'url' => 'https://energy.local-government-unit-1-ph.com/dashboard.php' . $auth_params, 'icon' => '💡']
                ];
                ?>
                <?php foreach($subsystem_dashboards as $dashboard): ?>
                <div class="col-12 col-lg-6 col-xl-4 mb-4">
                    <div class="card activity-card border-0 shadow-sm h-100 clickable-card" onclick="window.open('<?= $dashboard['url'] ?>', '_blank')" style="cursor: pointer;">
                        <div class="card-body p-4 text-center">
                            <div class="mb-3">
                                <div class="display-4 mb-3"><?= $dashboard['icon'] ?></div>
                                <h5 class="fw-bold text-lgu-headline mb-2"><?= $dashboard['name'] ?></h5>
                                <p class="text-muted mb-3">Access subsystem dashboard</p>
                                <span class="badge bg-primary">Click to Open Dashboard</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>


        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set current date
        document.addEventListener('DOMContentLoaded', function() {
            const dateElement = document.getElementById('current-date');
            if (dateElement) {
                const today = new Date();
                const options = { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                };
                dateElement.textContent = today.toLocaleDateString('en-US', options);
            }
        });

        // Mobile sidebar toggle
        document.getElementById('mobile-sidebar-toggle')?.addEventListener('click', function() {
            const sidebar = document.getElementById('admin-sidebar');
            if (sidebar) {
                sidebar.classList.toggle('show-mobile');
            }
        });

        // Sidebar close button
        document.getElementById('sidebar-close')?.addEventListener('click', function() {
            const sidebar = document.getElementById('admin-sidebar');
            if (sidebar) {
                sidebar.classList.remove('show-mobile');
            }
        });
    </script>
</body>
</html>
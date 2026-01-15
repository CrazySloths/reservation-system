<?php
// ========== LIVE DATABASE CONFIG ==========
define('DB_HOST', 'localhost');
define('DB_NAME', 'hous_lgu1_auth_db');
define('DB_USER', 'hous_laslee_mabato');
define('DB_PASS', 'o%8KgBsjFaOsj@LU');

// ========== LIVE REPORTS DATABASE ==========
define('REPORTS_DB_NAME', 'hous_lgu1_reports_db');

// ========== LIVE MAIL CONFIG ==========
define('MAIL_USERNAME', 'lancearon74@gmail.com');
define('MAIL_PASSWORD', 'haqszoohfgafjusa');

// ========== LIVE DOMAIN ==========
define('DOMAIN', 'local-government-unit-1-ph.com');

// ========== LIVE API ENDPOINTS ==========
define('HOUSING_API_URL', 'https://housing.local-government-unit-1-ph.com/api/');
define('UTILITY_API_URL', 'https://billing.local-government-unit-1-ph.com/api/');
define('LAND_API_URL', 'https://lang-reg.local-government-unit-1-ph.com/api/');
define('INFRA_API_URL', 'https://pm.local-government-unit-1-ph.com/api/');
define('ROAD_API_URL', 'https://road-trans.local-government-unit-1-ph.com/api/');
define('FACILITY_API_URL', 'https://facilities.local-government-unit-1-ph.com/api/');
define('MAINTENANCE_API_URL', 'https://community.local-government-unit-1-ph.com/api/');
define('URBAN_API_URL', 'https://planning.local-government-unit-1-ph.com/api/');
define('RENEWABLE_API_URL', 'https://renew-energy.local-government-unit-1-ph.com/api/');
define('ENERGY_API_URL', 'https://energy.local-government-unit-1-ph.com/api/');

// ========== LIVE DASHBOARD REDIRECTS ==========
define('SUBSYSTEMS', json_encode([
    'housing'       => 'https://housing.local-government-unit-1-ph.com/dashboard.php',
    'utility'       => 'https://billing.local-government-unit-1-ph.com/dashboard.php',
    'land'          => 'https://lang-reg.local-government-unit-1-ph.com/dashboard.php',
    'infrastructure'=> 'https://pm.local-government-unit-1-ph.com/dashboard.php',
    'road'          => 'https://road-trans.local-government-unit-1-ph.com/dashboard.php',
    'facility'      => 'https://facilities.local-government-unit-1-ph.com/dashboard.php',
    'maintenance'   => 'https://community.local-government-unit-1-ph.com/dashboard.php',
    'urban'         => 'https://planning.local-government-unit-1-ph.com/dashboard.php',
    'renewable'     => 'https://renew-energy.local-government-unit-1-ph.com/dashboard.php',
    'energy'        => 'https://energy.local-government-unit-1-ph.com/dashboard.php'
]));

// ========== LIVE TOKEN FOR INTERNAL API ==========
define('API_SECRET_TOKEN', 'live-auth-token-xyz-456');

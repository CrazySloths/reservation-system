<?php
// Detect environment based on host
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$env = (strpos($host, 'localhost') !== false || $host === '127.0.0.1') ? 'local' : 'live';

// Load environment-specific constants
require_once __DIR__ . "/env.$env.php";

// Database connection variables
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
$username = DB_USER;
$password = DB_PASS;
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

// Database connection using PDO
try {
    $conn = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $e->getMessage()
    ]));
}

// Reports database connection
$reports_dsn = "mysql:host=" . DB_HOST . ";dbname=lgu1_reports_db;charset=utf8mb4";
try {
    $reports_conn = new PDO($reports_dsn, $username, $password, $options);
} catch (PDOException $e) {
    // Reports DB connection is optional
    $reports_conn = null;
}

// Optional: API call helper
function callAPI($method, $url, $data = [], $auth = true) {
    $curl = curl_init();

    $headers = ['Content-Type: application/json'];
    if ($auth && defined('API_SECRET_TOKEN')) {
        $headers[] = 'Authorization: Bearer ' . API_SECRET_TOKEN;
    }

    if (strtoupper($method) === 'POST') {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    } else if (strtoupper($method) === 'GET' && !empty($data)) {
        $url .= '?' . http_build_query($data);
    }

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response, true);
}

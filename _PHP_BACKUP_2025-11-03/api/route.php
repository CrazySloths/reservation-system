<?php
require_once '../config/config.php';

// Circuit Breaker Configuration
class CircuitBreaker {
    private static $failures = [];
    private static $timeout = 5; // seconds
    private static $failure_threshold = 3;
    private static $recovery_timeout = 30; // seconds
    
    public static function call($endpoint, $fallback_data = null) {
        $key = md5($endpoint);
        $now = time();
        
        // Check if circuit is open
        if (isset(self::$failures[$key]) && 
            self::$failures[$key]['count'] >= self::$failure_threshold &&
            ($now - self::$failures[$key]['last_failure']) < self::$recovery_timeout) {
            return self::fallback($fallback_data);
        }
        
        // Attempt API call with cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::$timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, 'LGU1-API-Gateway');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($response === false || $http_code >= 400) {
            self::recordFailure($key);
            return self::fallback($fallback_data);
        }
        
        // Reset failures on success
        unset(self::$failures[$key]);
        return $response;
    }
    
    private static function recordFailure($key) {
        if (!isset(self::$failures[$key])) {
            self::$failures[$key] = ['count' => 0, 'last_failure' => 0];
        }
        self::$failures[$key]['count']++;
        self::$failures[$key]['last_failure'] = time();
    }
    
    private static function fallback($fallback_data) {
        return json_encode([
            'success' => false,
            'message' => 'Service temporarily unavailable',
            'fallback' => true,
            'data' => $fallback_data ?? []
        ]);
    }
}

// CORS Headers
$allowed_origins = [
    'https://housing.local-government-unit-1-ph.com',
    'https://applicant-housing.local-government-unit-1-ph.com',
    'https://pm.local-government-unit-1-ph.com',
    'https://energy.local-government-unit-1-ph.com',
    'https://road-trans.local-government-unit-1-ph.com',
    'https://lang-reg.local-government-unit-1-ph.com',
    'https://facilities.local-government-unit-1-ph.com',
    'https://community.local-government-unit-1-ph.com',
    'https://billing.local-government-unit-1-ph.com',
    'https://renew-energy.local-government-unit-1-ph.com',
    'https://planning.local-government-unit-1-ph.com',
    'https://qcitizen-homes.local-government-unit-1-ph.com',
    'https://lrts-staff.local-government-unit-1-ph.com',
    'https://lrts-citizen.local-government-unit-1-ph.com',
    'https://infra-pm.local-government-unit-1-ph.com'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
}
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['path'] ?? '';

switch ($path) {
    case 'users':
        require_once 'get-users.php';
        break;
        
    case 'barangays':
        require_once 'get_barangays.php';
        break;
        
    case 'districts':
        require_once 'get-districts.php';
        break;
        
    case 'subsystem-roles':
        require_once 'get_subsystem_roles.php';
        break;
        
    case 'infrastructure-users':
        require_once 'get-infrastructure-users.php';
        break;
        
    case 'utility-users':
        require_once 'get-utility-users.php';
        break;
        
    case 'transportation-users':
        require_once 'get-transportation-users.php';
        break;
        
    case 'facilities-users':
        require_once 'get-facilities-users.php';
        break;
        
    case 'community-users':
        require_once 'get-community-users.php';
        break;
        
    case 'planning-users':
        require_once 'get-planning-users.php';
        break;
        
    case 'land-users':
        require_once 'get-land-users.php';
        break;
        
    case 'housing-users':
        require_once 'get-housing-users.php';
        break;
        
    case 'renewable-users':
        require_once 'get-renewable-users.php';
        break;
        
    case 'energy-users':
        require_once 'get-energy-users.php';
        break;
        
    case 'citizen-data':
        require_once 'get-citizen-data.php';
        break;
        
    case 'citizen-documents':
        require_once 'get-citizen-documents.php';
        break;

    // housing and resettlements
        
    case 'relocation-requirements':
        $api_url = 'https://qcitizen-homes.local-government-unit-1-ph.com/api/relocationRequirements.php';
        $fallback_data = [
            'requirements' => [
                'Valid ID',
                'Proof of Income',
                'Certificate of Indigency',
                'Barangay Clearance'
            ]
        ];
        $response = CircuitBreaker::call($api_url, $fallback_data);
        echo $response;
        break;
        
    case 'get-beneficiaries':
        $api_url = 'https://qcitizen-homes.local-government-unit-1-ph.com/api/get-beneficiaries.php';
        $fallback_data = [];
        $response = CircuitBreaker::call($api_url, $fallback_data);
        echo $response;
        break;
        
    case 'get-housing-projects':
        $api_url = 'https://qcitizen-homes.local-government-unit-1-ph.com/api/get-housing-projects.php';
        $fallback_data = [];
        $response = CircuitBreaker::call($api_url, $fallback_data);
        echo $response;
        break;
        
    case 'get-housing-units':
        $api_url = 'https://qcitizen-homes.local-government-unit-1-ph.com/api/get-housing-units.php';
        $fallback_data = [];
        $response = CircuitBreaker::call($api_url, $fallback_data);
        echo $response;
        break;
        
    case 'get-resettlement-plans':
        $api_url = 'https://qcitizen-homes.local-government-unit-1-ph.com/api/get-resettlement-plans.php';
        $fallback_data = [];
        $response = CircuitBreaker::call($api_url, $fallback_data);
        echo $response;
        break;
        
    case 'get-proof-of-billing':
        $api_url = 'https://applicant-housing.local-government-unit-1-ph.com/api/integrations/utilitiesBilling/get-proof-of-billing.php';
        $fallback_data = [];
        $response = CircuitBreaker::call($api_url, $fallback_data);
        echo $response;
        break;
        
    case 'get-certificate-of-no-property':
        $api_url = 'https://applicant-housing.local-government-unit-1-ph.com/api/integrations/land/get-certificate-of-no-property.php';
        $fallback_data = [];
        $response = CircuitBreaker::call($api_url, $fallback_data);
        echo $response;
        break;
        
    case 'verify-property-ownership':
        $applicant_id = $_GET['applicant_id'] ?? '';
        $api_url = 'https://qcitizen-homes.local-government-unit-1-ph.com/api/integrations/land/verify-property-ownership.php?applicant_id=' . $applicant_id;
        $fallback_data = ['verification_status' => 'unavailable', 'message' => 'Service unavailable'];
        $response = CircuitBreaker::call($api_url, $fallback_data);
        echo $response;
        break;
        
    case 'setup-unit-utilities':
        if ($method === 'POST') {
            $api_url = 'https://qcitizen-homes.local-government-unit-1-ph.com/api/integrations/utilitiesBilling/setup-unit-utilities.php';
            $post_data = file_get_contents('php://input');
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            curl_close($ch);
            echo $response;
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
        break;
        
    case 'get-payment-tracking':
        $api_url = 'https://qcitizen-homes.local-government-unit-1-ph.com/api/integrations/utilitiesBilling/get-payment-tracking.php';
        $fallback_data = [];
        $response = CircuitBreaker::call($api_url, $fallback_data);
        echo $response;
        break;
        
    case 'sync-payment-data':
        if ($method === 'POST') {
            $api_url = 'https://qcitizen-homes.local-government-unit-1-ph.com/api/integrations/utilitiesBilling/sync-payment-data.php';
            $post_data = file_get_contents('php://input');
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            curl_close($ch);
            echo $response;
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
        break;
        
    case 'register-housing-units':
        if ($method === 'POST') {
            $api_url = 'https://qcitizen-homes.local-government-unit-1-ph.com/api/integrations/maintenance/register-housing-units.php';
            $post_data = file_get_contents('php://input');
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            curl_close($ch);
            echo $response;
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
        break;
        
    case 'coordinate-occupancy-requests':
        if ($method === 'POST') {
            $api_url = 'https://qcitizen-homes.local-government-unit-1-ph.com/api/integrations/maintenance/coordinate-occupancy-requests.php';
            $post_data = file_get_contents('php://input');
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            curl_close($ch);
            echo $response;
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
        break;
        
    case 'plan-resettlement-maintenance':
        if ($method === 'POST') {
            $api_url = 'https://qcitizen-homes.local-government-unit-1-ph.com/api/integrations/maintenance/plan-resettlement-maintenance.php';
            $post_data = file_get_contents('php://input');
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            curl_close($ch);
            echo $response;
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
        break;

    case 'reserve-orientation-facilities':
        if ($method === 'POST') {
            $api_url = 'https://qcitizen-homes.local-government-unit-1-ph.com/api/integrations/facilities/reserve-orientation-facilities.php';
            $post_data = file_get_contents('php://input');
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            curl_close($ch);
            echo $response;
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
        break;
        
    case 'book-resettlement-venues':
        if ($method === 'POST') {
            $api_url = 'https://qcitizen-homes.local-government-unit-1-ph.com/api/integrations/facilities/book-resettlement-venues.php';
            $post_data = file_get_contents('php://input');
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            curl_close($ch);
            echo $response;
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
        break;
        
    case 'reserve-community-events':
        if ($method === 'POST') {
            $api_url = 'https://qcitizen-homes.local-government-unit-1-ph.com/api/integrations/facilities/reserve-community-events.php';
            $post_data = file_get_contents('php://input');
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            curl_close($ch);
            echo $response;
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
        break;
        
    default:
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Endpoint not found'
        ]);
}
?>
<?php
function syncUserRegistration($user_id, $full_name, $email, $phone = '', $address = '') {
    $user_data = json_encode([
        'user_id' => $user_id
    ]);
    
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $user_data,
            'timeout' => 5
        ]
    ]);
    
    // Sync to Applicant Housing System
    @file_get_contents('http://localhost/LGU1-Applicant-HousingAndResettlement/api/sync-applicant.php', false, $context);
    
    // Sync to Housing Management System
    @file_get_contents('http://localhost/LGU1-HousingAndResettlementManagement/api/sync-applicant.php', false, $context);
}
?>
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Provider Selection
    |--------------------------------------------------------------------------
    |
    | Choose which AI provider to use: 'facepp' or 'azure'
    | Face++ is recommended - no credit card needed, free tier available
    |
    */

    'provider' => env('AI_PROVIDER', 'facepp'), // 'facepp' or 'azure'

    /*
    |--------------------------------------------------------------------------
    | Face++ API Configuration (Recommended - No credit card needed!)
    |--------------------------------------------------------------------------
    |
    | Configure your Face++ API credentials here.
    | Sign up at: https://www.faceplusplus.com/
    | Free tier: 1,000 API calls per month
    |
    */

    'facepp' => [
        'api_key' => env('FACEPP_API_KEY', ''),
        'api_secret' => env('FACEPP_API_SECRET', ''),
        'endpoint' => env('FACEPP_ENDPOINT', 'https://api-us.faceplusplus.com'), // or https://api-cn.faceplusplus.com
        'timeout' => env('FACEPP_TIMEOUT', 30), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Azure Face API Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your Azure Face API credentials here.
    | Get your credentials from: https://portal.azure.com
    |
    */

    'azure' => [
        'endpoint' => env('AZURE_FACE_ENDPOINT', 'https://YOUR-RESOURCE-NAME.cognitiveservices.azure.com'),
        'key' => env('AZURE_FACE_KEY', ''),
        'timeout' => env('AZURE_FACE_TIMEOUT', 30), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Verification Thresholds
    |--------------------------------------------------------------------------
    |
    | Configure AI verification thresholds for face matching and quality
    |
    */

    'thresholds' => [
        'face_match_confidence' => env('FACE_MATCH_THRESHOLD', 70), // 70% minimum similarity (Face++ uses 0-100 scale)
        'face_quality_min' => env('FACE_QUALITY_MIN', 50), // 50% minimum quality
        'liveness_confidence' => env('LIVENESS_CONFIDENCE', 50), // 50% minimum for liveness
    ],
];


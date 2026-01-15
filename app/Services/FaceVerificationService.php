<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class FaceVerificationService
{
    private $provider;
    private $timeout;
    
    // Face++ credentials
    private $faceppApiKey;
    private $faceppApiSecret;
    private $faceppEndpoint;
    
    // Azure credentials
    private $azureEndpoint;
    private $azureKey;

    public function __construct()
    {
        $this->provider = config('azure.provider', 'facepp');
        $this->timeout = 30;
        
        // Face++ configuration
        $this->faceppApiKey = config('azure.facepp.api_key');
        $this->faceppApiSecret = config('azure.facepp.api_secret');
        $this->faceppEndpoint = config('azure.facepp.endpoint');
        
        // Azure configuration
        $this->azureEndpoint = config('azure.azure.endpoint');
        $this->azureKey = config('azure.azure.key');
    }

    /**
     * Verify if the face in the selfie matches the face in the ID
     * 
     * @param string $idImageBase64 Base64 encoded ID image
     * @param string $selfieImageBase64 Base64 encoded selfie image
     * @return array Verification results
     */
    public function verifyFaceMatch($idImageBase64, $selfieImageBase64)
    {
        if ($this->provider === 'facepp') {
            return $this->verifyFaceMatchFacePP($idImageBase64, $selfieImageBase64);
        } else {
            return $this->verifyFaceMatchAzure($idImageBase64, $selfieImageBase64);
        }
    }

    /**
     * Verify face match using Face++ API
     */
    private function verifyFaceMatchFacePP($idImageBase64, $selfieImageBase64)
    {
        try {
            // Remove data:image prefix if present
            $idImageBase64 = $this->cleanBase64($idImageBase64);
            $selfieImageBase64 = $this->cleanBase64($selfieImageBase64);

            $url = rtrim($this->faceppEndpoint, '/') . '/facepp/v3/compare';
            
            $response = Http::timeout($this->timeout)
                ->asForm()
                ->post($url, [
                    'api_key' => $this->faceppApiKey,
                    'api_secret' => $this->faceppApiSecret,
                    'image_base64_1' => $idImageBase64,
                    'image_base64_2' => $selfieImageBase64
                ]);

            if (!$response->successful()) {
                $error = $response->json();
                throw new Exception($error['error_message'] ?? 'Face comparison failed');
            }

            $result = $response->json();

            // Check if faces were detected
            if (!isset($result['confidence'])) {
                throw new Exception('No faces detected for comparison');
            }

            $confidence = $result['confidence']; // 0-100 scale
            $threshold = config('azure.thresholds.face_match_confidence', 70);

            return [
                'success' => true,
                'is_identical' => $confidence >= $threshold,
                'confidence' => $confidence / 100, // Convert to 0-1 scale for consistency
                'face_match_score' => $confidence,
                'thresholds' => $result['thresholds'] ?? null
            ];

        } catch (Exception $e) {
            Log::error('Face++ verification failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'face_match_score' => 0,
                'confidence' => 0
            ];
        }
    }

    /**
     * Verify face match using Azure Face API (original implementation)
     */
    private function verifyFaceMatchAzure($idImageBase64, $selfieImageBase64)
    {
        try {
            // Detect face in ID image
            $idFaceId = $this->detectFaceAzure($idImageBase64);
            
            if (!$idFaceId) {
                return [
                    'success' => false,
                    'error' => 'No face detected in ID image',
                    'face_match_score' => 0,
                    'confidence' => 0
                ];
            }

            // Detect face in selfie image
            $selfieFaceId = $this->detectFaceAzure($selfieImageBase64);
            
            if (!$selfieFaceId) {
                return [
                    'success' => false,
                    'error' => 'No face detected in selfie image',
                    'face_match_score' => 0,
                    'confidence' => 0
                ];
            }

            // Verify if faces match
            $verifyResult = $this->verifyFacesAzure($idFaceId, $selfieFaceId);

            return [
                'success' => true,
                'is_identical' => $verifyResult['isIdentical'],
                'confidence' => $verifyResult['confidence'],
                'face_match_score' => $verifyResult['confidence'] * 100,
                'id_face_id' => $idFaceId,
                'selfie_face_id' => $selfieFaceId
            ];

        } catch (Exception $e) {
            Log::error('Azure face verification failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'face_match_score' => 0,
                'confidence' => 0
            ];
        }
    }

    /**
     * Detect face using Azure API
     */
    private function detectFaceAzure($imageBase64)
    {
        try {
            $imageBase64 = $this->cleanBase64($imageBase64);
            $imageData = base64_decode($imageBase64);

            if (!$imageData) {
                throw new Exception('Failed to decode image data');
            }

            $url = rtrim($this->azureEndpoint, '/') . '/face/v1.0/detect';
            
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Ocp-Apim-Subscription-Key' => $this->azureKey,
                    'Content-Type' => 'application/octet-stream'
                ])
                ->withQueryParameters([
                    'returnFaceId' => 'true',
                    'returnFaceLandmarks' => 'false',
                    'returnFaceAttributes' => 'quality,blur,exposure,noise',
                ])
                ->withBody($imageData, 'application/octet-stream')
                ->post($url);

            if (!$response->successful()) {
                $error = $response->json();
                throw new Exception($error['error']['message'] ?? 'Face detection failed');
            }

            $faces = $response->json();

            if (empty($faces)) {
                return null;
            }

            return $faces[0]['faceId'] ?? null;

        } catch (Exception $e) {
            Log::error('Azure face detection failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify if two faces match using Azure
     */
    private function verifyFacesAzure($faceId1, $faceId2)
    {
        try {
            $url = rtrim($this->azureEndpoint, '/') . '/face/v1.0/verify';
            
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Ocp-Apim-Subscription-Key' => $this->azureKey,
                    'Content-Type' => 'application/json'
                ])
                ->post($url, [
                    'faceId1' => $faceId1,
                    'faceId2' => $faceId2
                ]);

            if (!$response->successful()) {
                $error = $response->json();
                throw new Exception($error['error']['message'] ?? 'Face verification failed');
            }

            return $response->json();

        } catch (Exception $e) {
            Log::error('Azure face verification failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if AI provider is configured
     * 
     * @return bool
     */
    public function isConfigured()
    {
        if ($this->provider === 'facepp') {
            return !empty($this->faceppApiKey) && !empty($this->faceppApiSecret);
        } else {
            return !empty($this->azureEndpoint) && 
                   !empty($this->azureKey) && 
                   $this->azureEndpoint !== 'https://YOUR-RESOURCE-NAME.cognitiveservices.azure.com';
        }
    }

    /**
     * Calculate perceptual hash for duplicate detection
     * This runs locally, not on any cloud service
     * 
     * @param string $imageBase64 Base64 encoded image
     * @return string Perceptual hash
     */
    public function calculatePerceptualHash($imageBase64)
    {
        try {
            $imageBase64 = $this->cleanBase64($imageBase64);
            $imageData = base64_decode($imageBase64);
            
            if (!$imageData) {
                throw new Exception('Failed to decode image for hashing');
            }

            // Create image from string
            $image = @imagecreatefromstring($imageData);
            
            if (!$image) {
                throw new Exception('Failed to create image from data');
            }

            // Resize to 8x8 for perceptual hashing
            $resized = imagecreatetruecolor(8, 8);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, 8, 8, imagesx($image), imagesy($image));
            
            // Convert to grayscale and calculate hash
            $hash = '';
            $pixels = [];
            
            for ($y = 0; $y < 8; $y++) {
                for ($x = 0; $x < 8; $x++) {
                    $rgb = imagecolorat($resized, $x, $y);
                    $gray = (($rgb >> 16) & 0xFF) * 0.299 + (($rgb >> 8) & 0xFF) * 0.587 + ($rgb & 0xFF) * 0.114;
                    $pixels[] = $gray;
                }
            }
            
            // Calculate average
            $avg = array_sum($pixels) / count($pixels);
            
            // Generate hash based on average
            foreach ($pixels as $pixel) {
                $hash .= ($pixel >= $avg) ? '1' : '0';
            }
            
            // Convert binary to hex
            $hexHash = '';
            for ($i = 0; $i < strlen($hash); $i += 4) {
                $hexHash .= base_convert(substr($hash, $i, 4), 2, 16);
            }
            
            imagedestroy($image);
            imagedestroy($resized);
            
            return $hexHash;

        } catch (Exception $e) {
            Log::error('Perceptual hash calculation failed: ' . $e->getMessage());
            
            // Fallback to simple hash
            return hash('sha256', $imageBase64);
        }
    }

    /**
     * Perform complete AI verification
     * 
     * @param array $images Array with 'id_front', 'id_back', 'selfie' base64 images
     * @return array Complete verification results
     */
    public function completeVerification($images)
    {
        $results = [
            'timestamp' => now()->toIso8601String(),
            'status' => 'pending',
            'provider' => $this->provider,
            'face_match_score' => 0,
            'id_authenticity_score' => 0,
            'liveness_score' => 0,
            'overall_confidence' => 0,
            'notes' => [],
            'hashes' => []
        ];

        try {
            // Check if AI provider is configured
            if (!$this->isConfigured()) {
                $results['status'] = 'manual_review';
                $results['notes'][] = 'AI provider not configured - manual review required';
                $results['error'] = ucfirst($this->provider) . ' API credentials not configured';
                
                // Still calculate hashes for duplicate detection
                $results['hashes']['id_front'] = $this->calculatePerceptualHash($images['id_front']);
                $results['hashes']['id_back'] = $this->calculatePerceptualHash($images['id_back']);
                $results['hashes']['selfie'] = $this->calculatePerceptualHash($images['selfie']);
                
                return $results;
            }

            // Calculate perceptual hashes for duplicate detection
            $results['hashes']['id_front'] = $this->calculatePerceptualHash($images['id_front']);
            $results['hashes']['id_back'] = $this->calculatePerceptualHash($images['id_back']);
            $results['hashes']['selfie'] = $this->calculatePerceptualHash($images['selfie']);

            // Verify face match between ID and selfie
            $faceMatch = $this->verifyFaceMatch($images['id_front'], $images['selfie']);

            if (!$faceMatch['success']) {
                $results['status'] = 'failed';
                $results['notes'][] = $faceMatch['error'];
                $results['error'] = $faceMatch['error'];
                return $results;
            }

            $results['face_match_score'] = $faceMatch['face_match_score'];
            $results['confidence'] = $faceMatch['confidence'];
            $results['is_identical'] = $faceMatch['is_identical'];

            // Determine status based on confidence
            $threshold = config('azure.thresholds.face_match_confidence', 70);
            
            if ($faceMatch['face_match_score'] >= $threshold) {
                $results['status'] = 'passed';
                $results['notes'][] = 'Face match verified successfully';
            } else if ($faceMatch['face_match_score'] >= ($threshold - 10)) {
                $results['status'] = 'manual_review';
                $results['notes'][] = 'Low confidence - requires manual review';
            } else {
                $results['status'] = 'failed';
                $results['notes'][] = 'Face in ID does not match selfie';
            }

            // Calculate overall scores
            $results['id_authenticity_score'] = 85; // Placeholder - requires additional verification
            $results['liveness_score'] = 75; // Placeholder - requires liveness detection API
            $results['overall_confidence'] = ($results['face_match_score'] + $results['id_authenticity_score'] + $results['liveness_score']) / 3;

            return $results;

        } catch (Exception $e) {
            Log::error('Complete verification failed: ' . $e->getMessage());
            
            $results['status'] = 'manual_review';
            $results['error'] = $e->getMessage();
            $results['notes'][] = 'Verification error - manual review required';
            
            return $results;
        }
    }

    /**
     * Clean base64 string (remove data:image prefix if present)
     */
    private function cleanBase64($base64String)
    {
        if (strpos($base64String, 'data:image') === 0) {
            return preg_replace('/^data:image\/\w+;base64,/', '', $base64String);
        }
        return $base64String;
    }
}

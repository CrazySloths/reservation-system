<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING EMAIL LOGO RENDERING ===\n\n";

// Test the path resolution
$logoPath = public_path('assets/images/logo-email.png');
echo "Logo path: $logoPath\n";
echo "File exists: " . (file_exists($logoPath) ? 'YES' : 'NO') . "\n";

if (file_exists($logoPath)) {
    $size = filesize($logoPath);
    echo "File size: " . number_format($size / 1024, 2) . " KB\n\n";
    
    // Test base64 encoding
    $logoBase64 = base64_encode(file_get_contents($logoPath));
    echo "Base64 length: " . number_format(strlen($logoBase64)) . " characters\n";
    echo "First 100 chars: " . substr($logoBase64, 0, 100) . "...\n\n";
    
    // Test rendering the Blade template
    echo "Testing Blade rendering...\n";
    
    $html = view('emails.layout', [
        'slot' => '<p>Test content</p>'
    ])->render();
    
    // Check if base64 is in the rendered HTML
    if (strpos($html, 'data:image/png;base64,') !== false) {
        echo "✓ Base64 image found in HTML!\n";
        
        // Extract and show a preview
        preg_match('/data:image\/png;base64,([A-Za-z0-9+\/=]+)/', $html, $matches);
        if (isset($matches[1])) {
            echo "Base64 in HTML length: " . number_format(strlen($matches[1])) . " characters\n";
        }
    } else {
        echo "✗ Base64 image NOT found in HTML!\n";
        echo "Checking for fallback text...\n";
        if (strpos($html, '>LGU<') !== false) {
            echo "✗ Fallback 'LGU' text is being used instead!\n";
        }
    }
    
} else {
    echo "✗ Logo file does not exist at: $logoPath\n";
}


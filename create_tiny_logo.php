<?php

echo "=== CREATING ULTRA-SMALL LOGO FOR GMAIL ===\n\n";

$originalLogo = 'public/assets/images/logo.png';

if (!file_exists($originalLogo)) {
    echo "ERROR: Logo not found!\n";
    exit(1);
}

if (!extension_loaded('gd')) {
    echo "ERROR: GD extension not loaded!\n";
    exit(1);
}

$img = imagecreatefrompng($originalLogo);
if (!$img) {
    echo "ERROR: Could not load image!\n";
    exit(1);
}

// Create TINY version for email (50x50)
$tinyLogo = 'public/assets/images/logo-tiny.png';
$newWidth = 50;
$newHeight = 50;

$thumb = imagecreatetruecolor($newWidth, $newHeight);
imagealphablending($thumb, false);
imagesavealpha($thumb, true);

imagecopyresampled(
    $thumb, $img,
    0, 0, 0, 0,
    $newWidth, $newHeight,
    imagesx($img), imagesy($img)
);

// Save with maximum compression
imagepng($thumb, $tinyLogo, 9);

imagedestroy($img);
imagedestroy($thumb);

echo "Original: " . number_format(filesize($originalLogo) / 1024, 2) . " KB\n";
echo "Tiny: " . number_format(filesize($tinyLogo) / 1024, 2) . " KB\n\n";

// Test base64 size
$base64 = base64_encode(file_get_contents($tinyLogo));
echo "Base64 size: ~" . number_format(strlen($base64) / 1024, 2) . " KB\n\n";

echo "✓ Ultra-small logo created!\n";


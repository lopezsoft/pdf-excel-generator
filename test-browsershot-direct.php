<?php

require __DIR__ . '/vendor/autoload.php';

use Spatie\Browsershot\Browsershot;

echo "=== TEST BROWSERSHOT DIRECTO ===\n\n";

$html = '<!DOCTYPE html>
<html>
<body>
    <h1>Test Browsershot</h1>
    <p>Prueba directa de Browsershot</p>
</body>
</html>';

try {
    echo "1. Testing Browsershot directly...\n";
    
    $pdf = Browsershot::html($html)
        ->format('A4')
        ->margins(10, 10, 10, 10)
        ->pdf();
    
    echo "   Size: " . strlen($pdf) . " bytes\n";
    echo "   Header: " . substr($pdf, 0, 4) . "\n";
    
    if (substr($pdf, 0, 4) === '%PDF') {
        echo "   ✓ Valid PDF!\n";
        file_put_contents(__DIR__ . '/test-output/browsershot-direct.pdf', $pdf);
        echo "   Saved to: test-output/browsershot-direct.pdf\n";
    } else {
        echo "   ✗ Invalid PDF\n";
        echo "   First 20 bytes (hex): " . bin2hex(substr($pdf, 0, 20)) . "\n";
    }
    
} catch (\Throwable $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n=== END ===\n";

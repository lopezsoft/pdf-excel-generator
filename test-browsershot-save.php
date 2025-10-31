<?php

require __DIR__ . '/vendor/autoload.php';

use Spatie\Browsershot\Browsershot;

echo "=== TEST BROWSERSHOT SAVE TO FILE ===\n\n";

$html = '<!DOCTYPE html>
<html>
<body>
    <h1>Test PDF</h1>
    <p>Testing direct file save</p>
</body>
</html>';

try {
    $outputFile = __DIR__ . '/test-output/browsershot-save-method.pdf';
    
    echo "1. Using save() method...\n";
    
    Browsershot::html($html)
        ->format('A4')
        ->margins(10, 10, 10, 10)
        ->save($outputFile);
    
    echo "   ✓ File saved: {$outputFile}\n";
    
    // Verificar el archivo
    $content = file_get_contents($outputFile);
    echo "   Size: " . strlen($content) . " bytes\n";
    echo "   Header: " . substr($content, 0, 4) . "\n";
    
    if (substr($content, 0, 4) === '%PDF') {
        echo "   ✓ Valid PDF header!\n";
    } else {
        echo "   ✗ Invalid header\n";
    }
    
} catch (\Throwable $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n=== END ===\n";

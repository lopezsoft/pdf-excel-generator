<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Lopezsoft\PdfExcelGenerator\Exporters\PdfExporter;

echo "=== TEST PDF SIMPLE ===\n\n";

// HTML ultra simple
$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test PDF</title>
</head>
<body>
    <h1>Hello World</h1>
    <p>Este es un PDF de prueba simple.</p>
</body>
</html>';

echo "1. Generating simple PDF...\n";

try {
    $pdfExporter = new PdfExporter();
    $pdfExporter->setHtml($html)->setFormat('A4');
    
    $content = $pdfExporter->stream();
    
    echo "   Generated: " . strlen($content) . " bytes\n";
    
    // Verificar header
    $header = substr($content, 0, 10);
    echo "   Header (hex): " . bin2hex($header) . "\n";
    echo "   Header (text): " . substr($content, 0, 4) . "\n";
    
    if (substr($content, 0, 4) === '%PDF') {
        echo "   ✓ Valid PDF header\n";
    } else {
        echo "   ✗ INVALID PDF header!\n";
        echo "   First 50 bytes:\n";
        echo "   " . bin2hex(substr($content, 0, 50)) . "\n";
    }
    
    // Guardar
    $filename = __DIR__ . '/test-output/simple.pdf';
    file_put_contents($filename, $content);
    echo "   Saved to: {$filename}\n";
    
} catch (\Throwable $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
    echo "   Trace:\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== END ===\n";

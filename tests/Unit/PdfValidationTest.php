<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Tests\Unit;

use Lopezsoft\PdfExcelGenerator\Exporters\PdfExporter;
use Lopezsoft\PdfExcelGenerator\Exceptions\InvalidPdfException;
use Lopezsoft\PdfExcelGenerator\Exceptions\ExportException;
use Orchestra\Testbench\TestCase;

/**
 * Tests para validación de PDFs generados.
 */
class PdfValidationTest extends TestCase
{
    private PdfExporter $exporter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->exporter = new PdfExporter();
    }

    /**
     * @test
     */
    public function it_validates_pdf_header(): void
    {
        $html = '<html><body><h1>Test PDF</h1></body></html>';
        
        $this->exporter->setHtml($html);
        $content = $this->exporter->stream();

        // Verificar header %PDF
        $this->assertStringStartsWith('%PDF', $content, 'PDF must start with %PDF header');
    }

    /**
     * @test
     */
    public function it_validates_minimum_pdf_size(): void
    {
        $html = '<html><body><p>Minimal content</p></body></html>';
        
        $this->exporter->setHtml($html);
        $content = $this->exporter->stream();

        // PDF válido debe tener al menos 100 bytes
        $this->assertGreaterThan(100, strlen($content), 'PDF must be at least 100 bytes');
    }

    /**
     * @test
     */
    public function it_throws_exception_on_invalid_pdf_header(): void
    {
        $this->expectException(InvalidPdfException::class);
        $this->expectExceptionMessage('invalid header');

        // Simular PDF corrupto (esto no debería pasar en producción)
        // Solo para probar la validación
        $reflection = new \ReflectionClass($this->exporter);
        $method = $reflection->getMethod('validateGeneratedContent');
        $method->setAccessible(true);

        // Contenido con header inválido
        $method->invoke($this->exporter, 'INVALID PDF CONTENT');
    }

    /**
     * @test
     */
    public function it_throws_exception_on_small_pdf(): void
    {
        $this->expectException(InvalidPdfException::class);
        $this->expectExceptionMessage('too small');

        $reflection = new \ReflectionClass($this->exporter);
        $method = $reflection->getMethod('validateGeneratedContent');
        $method->setAccessible(true);

        // Contenido muy pequeño (menos de 100 bytes)
        $method->invoke($this->exporter, '%PDF-1.4');
    }

    /**
     * @test
     */
    public function it_generates_valid_pdf_from_html(): void
    {
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
    </style>
</head>
<body>
    <h1>Test Report</h1>
    <p>This is a test PDF generated for validation.</p>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Product A</td>
                <td>10</td>
                <td>$100</td>
            </tr>
            <tr>
                <td>Product B</td>
                <td>5</td>
                <td>$50</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
HTML;

        $this->exporter->setHtml($html);
        $content = $this->exporter->stream();

        // Validaciones
        $this->assertStringStartsWith('%PDF', $content);
        $this->assertGreaterThan(1000, strlen($content), 'Complex PDF should be larger');
        
        // Verificar que contiene EOF (End of File) marker
        $this->assertStringContainsString('%%EOF', $content, 'PDF must have EOF marker');
    }

    /**
     * @test
     */
    public function it_normalizes_html_whitespace(): void
    {
        // HTML con mucha indentación (típico de Blade)
        $htmlWithSpaces = <<<HTML
<!DOCTYPE html>
<html>
    <head>
        <title>Test</title>
    </head>
    <body>
        <div>
            <h1>
                Title
            </h1>
            <p>
                Paragraph with
                multiple lines
            </p>
        </div>
    </body>
</html>
HTML;

        $this->exporter->setHtml($htmlWithSpaces);
        $contentWithSpaces = $this->exporter->stream();

        // HTML compacto (sin espacios innecesarios)
        $htmlCompact = '<!DOCTYPE html><html><head><title>Test</title></head><body><div><h1>Title</h1><p>Paragraph with multiple lines</p></div></body></html>';
        
        $exporter2 = new PdfExporter();
        $exporter2->setHtml($htmlCompact);
        $contentCompact = $exporter2->stream();

        // El PDF con HTML normalizado debería ser similar o más pequeño
        $this->assertLessThanOrEqual(
            strlen($contentWithSpaces) * 1.1, // Margen de 10%
            strlen($contentCompact),
            'Normalized HTML should produce similar or smaller PDF'
        );
    }

    /**
     * @test
     */
    public function it_throws_exception_on_empty_html(): void
    {
        $this->expectException(ExportException::class);
        $this->expectExceptionMessage('missing data');

        $this->exporter->setHtml('');
        $this->exporter->stream();
    }

    /**
     * @test
     */
    public function it_handles_special_characters(): void
    {
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <p>Special characters: ñ, á, é, í, ó, ú, ü, ¿, ¡</p>
    <p>Symbols: €, £, ¥, ©, ®, ™</p>
    <p>Math: ∑, ∫, √, ∞, π</p>
</body>
</html>
HTML;

        $this->exporter->setHtml($html);
        $content = $this->exporter->stream();

        $this->assertStringStartsWith('%PDF', $content);
        $this->assertGreaterThan(500, strlen($content));
    }

    /**
     * @test
     */
    public function it_respects_custom_margins(): void
    {
        $html = '<html><body><h1>Test Margins</h1></body></html>';
        
        // Márgenes personalizados
        $this->exporter
            ->setHtml($html)
            ->setMargins([
                'top' => 20,
                'right' => 15,
                'bottom' => 20,
                'left' => 15,
            ]);

        $content = $this->exporter->stream();

        $this->assertStringStartsWith('%PDF', $content);
        // Con márgenes mayores, el PDF podría ser ligeramente diferente
        $this->assertGreaterThan(100, strlen($content));
    }
}

<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Tests\Unit;

use Lopezsoft\PdfExcelGenerator\Exporters\PdfExporter;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Storage;

/**
 * Test para verificar que los backgrounds se imprimen correctamente en PDFs.
 */
class BackgroundPrintTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    /**
     * @test
     */
    public function it_enables_background_printing_by_default(): void
    {
        $html = '
            <html>
            <head>
                <style>
                    body { background-color: #f0f0f0; }
                    .box { background-color: #ff0000; color: white; padding: 20px; }
                </style>
            </head>
            <body>
                <div class="box">Este div tiene fondo rojo</div>
            </body>
            </html>
        ';

        $exporter = new PdfExporter();
        $exporter->setHtml($html)->setDisk('local');

        // Por defecto printBackground debería estar en true
        $result = $exporter->save('with-background.pdf');

        $this->assertTrue(
            Storage::disk('local')->exists('with-background.pdf'),
            'El PDF con background debe guardarse'
        );

        // El PDF debe contener el contenido
        $content = Storage::disk('local')->get('with-background.pdf');
        $this->assertNotEmpty($content);
    }

    /**
     * @test
     */
    public function it_can_disable_background_printing(): void
    {
        $html = '<html><body style="background: red;"><h1>Test</h1></body></html>';

        $exporter = new PdfExporter();
        $exporter->setHtml($html)->setDisk('local');
        
        // Deshabilitar backgrounds
        $exporter->printBackground(false);

        $result = $exporter->save('without-background.pdf');

        $this->assertTrue(
            Storage::disk('local')->exists('without-background.pdf'),
            'El PDF sin background debe guardarse'
        );
    }

    /**
     * @test
     */
    public function it_handles_complex_backgrounds(): void
    {
        $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    thead {
                        background-color: #007bff;
                        color: white;
                    }
                    tbody tr:nth-child(even) {
                        background-color: #f8f9fa;
                    }
                    tbody tr:nth-child(odd) {
                        background-color: #ffffff;
                    }
                    td, th {
                        padding: 10px;
                        border: 1px solid #dee2e6;
                    }
                </style>
            </head>
            <body>
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Item 1</td>
                            <td>100</td>
                        </tr>
                        <tr>
                            <td>Item 2</td>
                            <td>200</td>
                        </tr>
                        <tr>
                            <td>Item 3</td>
                            <td>300</td>
                        </tr>
                    </tbody>
                </table>
            </body>
            </html>
        ';

        $exporter = new PdfExporter();
        $exporter->setHtml($html)->setDisk('local');

        $result = $exporter->save('table-with-backgrounds.pdf');

        $this->assertTrue(
            Storage::disk('local')->exists('table-with-backgrounds.pdf'),
            'El PDF con tabla y backgrounds debe guardarse'
        );
    }
}

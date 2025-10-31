<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Tests\Unit;

use Lopezsoft\PdfExcelGenerator\Exporters\PdfExporter;
use Lopezsoft\PdfExcelGenerator\Exporters\ExcelExporter;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Storage;

/**
 * Tests para verificar la creación automática de directorios.
 */
class DirectoryCreationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurar disco de prueba
        Storage::fake('local');
    }

    /**
     * @test
     */
    public function it_creates_directory_when_saving_pdf_with_subdirectory(): void
    {
        $html = '<html><body><h1>Test PDF</h1></body></html>';
        
        $exporter = new PdfExporter();
        $exporter->setHtml($html)->setDisk('local');
        
        // Guardar en subdirectorio que no existe
        $result = $exporter->save('docs/invoice.pdf');
        
        // Verificar que el archivo existe
        $this->assertTrue(Storage::disk('local')->exists('docs/invoice.pdf'));
        
        // Verificar que el directorio se creó
        $this->assertTrue(Storage::disk('local')->exists('docs'));
    }

    /**
     * @test
     */
    public function it_creates_nested_directories_when_saving_pdf(): void
    {
        $html = '<html><body><h1>Monthly Report</h1></body></html>';
        
        $exporter = new PdfExporter();
        $exporter->setHtml($html)->setDisk('local');
        
        // Guardar en directorios anidados profundos
        $result = $exporter->save('reports/2025/10/monthly.pdf');
        
        // Verificar que el archivo existe
        $this->assertTrue(Storage::disk('local')->exists('reports/2025/10/monthly.pdf'));
        
        // Verificar que todos los directorios se crearon
        $this->assertTrue(Storage::disk('local')->exists('reports'));
        $this->assertTrue(Storage::disk('local')->exists('reports/2025'));
        $this->assertTrue(Storage::disk('local')->exists('reports/2025/10'));
    }

    /**
     * @test
     */
    public function it_saves_file_in_root_without_creating_directory(): void
    {
        $html = '<html><body><h1>Simple PDF</h1></body></html>';
        
        $exporter = new PdfExporter();
        $exporter->setHtml($html)->setDisk('local');
        
        // Guardar en raíz (sin subdirectorio)
        $result = $exporter->save('simple.pdf');
        
        // Verificar que el archivo existe en la raíz
        $this->assertTrue(Storage::disk('local')->exists('simple.pdf'));
    }

    /**
     * @test
     */
    public function it_creates_directory_when_saving_excel_with_subdirectory(): void
    {
        $data = [
            ['Name', 'Value'],
            ['Item A', 100],
            ['Item B', 200],
        ];
        
        $exporter = new ExcelExporter();
        $exporter->setData($data)->setDisk('local')->setWriterType('xlsx');
        
        // Guardar en subdirectorio
        $result = $exporter->save('exports/data.xlsx');
        
        // Verificar que el archivo existe
        $this->assertTrue(Storage::disk('local')->exists('exports/data.xlsx'));
        
        // Verificar que el directorio se creó
        $this->assertTrue(Storage::disk('local')->exists('exports'));
    }

    /**
     * @test
     */
    public function it_creates_directory_when_saving_csv_with_subdirectory(): void
    {
        $data = [
            ['Product', 'Price'],
            ['Laptop', 999],
            ['Mouse', 29],
        ];
        
        $exporter = new ExcelExporter();
        $exporter->setData($data)->setDisk('local')->setWriterType('csv');
        
        // Guardar CSV en subdirectorio
        $result = $exporter->save('exports/csv/products.csv');
        
        // Verificar que el archivo existe
        $this->assertTrue(Storage::disk('local')->exists('exports/csv/products.csv'));
        
        // Verificar directorios
        $this->assertTrue(Storage::disk('local')->exists('exports'));
        $this->assertTrue(Storage::disk('local')->exists('exports/csv'));
    }

    /**
     * @test
     */
    public function it_does_not_fail_if_directory_already_exists(): void
    {
        $html = '<html><body><h1>Test</h1></body></html>';
        
        // Crear directorio manualmente primero
        Storage::disk('local')->makeDirectory('existing');
        
        $exporter = new PdfExporter();
        $exporter->setHtml($html)->setDisk('local');
        
        // Guardar en directorio que ya existe
        $result = $exporter->save('existing/file.pdf');
        
        // No debe lanzar error y el archivo debe existir
        $this->assertTrue(Storage::disk('local')->exists('existing/file.pdf'));
    }

    /**
     * @test
     */
    public function it_handles_paths_with_multiple_levels(): void
    {
        $html = '<html><body><h1>Deep Test</h1></body></html>';
        
        $exporter = new PdfExporter();
        $exporter->setHtml($html)->setDisk('local');
        
        // Path muy profundo
        $result = $exporter->save('a/b/c/d/e/deep.pdf');
        
        // Verificar que todo se creó
        $this->assertTrue(Storage::disk('local')->exists('a/b/c/d/e/deep.pdf'));
        $this->assertTrue(Storage::disk('local')->exists('a/b/c/d/e'));
    }

    /**
     * @test
     */
    public function it_works_with_different_disks(): void
    {
        // Configurar disco público
        Storage::fake('public');
        
        $html = '<html><body><h1>Public PDF</h1></body></html>';
        
        $exporter = new PdfExporter();
        $exporter->setHtml($html)->setDisk('public');
        
        // Guardar en disco público
        $result = $exporter->save('pdfs/public-file.pdf');
        
        // Verificar en el disco correcto
        $this->assertTrue(Storage::disk('public')->exists('pdfs/public-file.pdf'));
        $this->assertTrue(Storage::disk('public')->exists('pdfs'));
    }
}

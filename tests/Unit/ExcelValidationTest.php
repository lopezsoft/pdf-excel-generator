<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Tests\Unit;

use Lopezsoft\PdfExcelGenerator\Exporters\ExcelExporter;
use Lopezsoft\PdfExcelGenerator\Exceptions\ExportException;
use Orchestra\Testbench\TestCase;

/**
 * Tests para validación de archivos Excel generados.
 */
class ExcelValidationTest extends TestCase
{
    private ExcelExporter $exporter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->exporter = new ExcelExporter();
    }

    /**
     * @test
     */
    public function it_generates_valid_xlsx_file(): void
    {
        $data = [
            ['Name', 'Age', 'Email'],
            ['John Doe', 30, 'john@example.com'],
            ['Jane Smith', 25, 'jane@example.com'],
        ];

        $this->exporter
            ->setData($data)
            ->setWriterType('xlsx');

        $content = $this->exporter->stream();

        // XLSX es un archivo ZIP (comienza con PK)
        $this->assertStringStartsWith('PK', $content, 'XLSX must start with PK signature (ZIP format)');
        
        // Tamaño mínimo razonable
        $this->assertGreaterThan(1000, strlen($content), 'XLSX must be at least 1KB');
    }

    /**
     * @test
     */
    public function it_generates_valid_csv_file(): void
    {
        $data = [
            ['Product', 'Price', 'Stock'],
            ['Item A', 100, 50],
            ['Item B', 200, 30],
        ];

        $this->exporter
            ->setData($data)
            ->setWriterType('csv');

        $content = $this->exporter->stream();

        // CSV debe contener comas y saltos de línea
        $this->assertStringContainsString(',', $content, 'CSV must contain commas');
        $this->assertStringContainsString("\n", $content, 'CSV must contain line breaks');
        
        // Verificar que contiene los datos
        $this->assertStringContainsString('Product', $content);
        $this->assertStringContainsString('Item A', $content);
        $this->assertStringContainsString('Item B', $content);
    }

    /**
     * @test
     */
    public function it_validates_minimum_excel_size(): void
    {
        $data = [
            ['A1', 'B1'],
            ['A2', 'B2'],
        ];

        $this->exporter
            ->setData($data)
            ->setWriterType('xlsx');

        $content = $this->exporter->stream();

        // Excel vacío tiene ~3KB de overhead
        $this->assertGreaterThan(100, strlen($content));
    }

    /**
     * @test
     */
    public function it_throws_exception_on_empty_data(): void
    {
        $this->expectException(ExportException::class);
        $this->expectExceptionMessage('missing data');

        $this->exporter->setData([]);
        $this->exporter->stream();
    }

    /**
     * @test
     */
    public function it_handles_large_datasets(): void
    {
        // Generar 1000 filas de datos
        $data = [['ID', 'Name', 'Value', 'Description']];
        
        for ($i = 1; $i <= 1000; $i++) {
            $data[] = [
                $i,
                "Item {$i}",
                rand(100, 9999),
                "Description for item {$i}",
            ];
        }

        $this->exporter
            ->setData($data)
            ->setWriterType('xlsx');

        $content = $this->exporter->stream();

        $this->assertStringStartsWith('PK', $content);
        $this->assertGreaterThan(10000, strlen($content), 'Large dataset should produce substantial file');
    }

    /**
     * @test
     */
    public function it_handles_special_characters_in_excel(): void
    {
        $data = [
            ['Text', 'Number', 'Symbol'],
            ['ñáéíóú', 123.45, '€£¥'],
            ['中文', 678.90, '©®™'],
        ];

        $this->exporter
            ->setData($data)
            ->setWriterType('xlsx');

        $content = $this->exporter->stream();

        $this->assertStringStartsWith('PK', $content);
        $this->assertGreaterThan(1000, strlen($content));
    }

    /**
     * @test
     */
    public function it_supports_numeric_and_string_data(): void
    {
        $data = [
            ['String', 'Integer', 'Float', 'Boolean'],
            ['Text', 42, 3.14, true],
            ['Another', 0, -99.99, false],
        ];

        $this->exporter
            ->setData($data)
            ->setWriterType('xlsx');

        $content = $this->exporter->stream();

        $this->assertStringStartsWith('PK', $content);
    }

    /**
     * @test
     */
    public function it_generates_different_formats(): void
    {
        $data = [
            ['A', 'B'],
            [1, 2],
        ];

        // XLSX
        $this->exporter->setData($data)->setWriterType('xlsx');
        $xlsx = $this->exporter->stream();
        $this->assertStringStartsWith('PK', $xlsx);

        // CSV
        $exporter2 = new ExcelExporter();
        $exporter2->setData($data)->setWriterType('csv');
        $csv = $exporter2->stream();
        $this->assertStringContainsString(',', $csv);

        // Los formatos deben ser diferentes
        $this->assertNotEquals($xlsx, $csv);
    }
}

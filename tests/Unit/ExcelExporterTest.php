<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Tests\Unit;

use Lopezsoft\PdfExcelGenerator\Exporters\ExcelExporter;
use PHPUnit\Framework\TestCase;

/**
 * Test para ExcelExporter.
 */
class ExcelExporterTest extends TestCase
{
    private ExcelExporter $exporter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->exporter = new ExcelExporter();
    }

    public function test_validates_with_data(): void
    {
        $this->exporter->setData([
            ['Name', 'Age'],
            ['John', 30],
            ['Jane', 25],
        ]);

        $this->assertTrue($this->exporter->validate());
    }

    public function test_validates_without_data(): void
    {
        $this->assertFalse($this->exporter->validate());
    }

    public function test_can_set_writer_type(): void
    {
        $result = $this->exporter->setWriterType('csv');
        $this->assertInstanceOf(ExcelExporter::class, $result);
    }

    public function test_can_set_sheet_title(): void
    {
        $result = $this->exporter->setSheetTitle('My Sheet');
        $this->assertInstanceOf(ExcelExporter::class, $result);
    }

    public function test_can_chain_methods(): void
    {
        $result = $this->exporter
            ->setData([['A', 'B']])
            ->setWriterType('xlsx')
            ->setSheetTitle('Test')
            ->setFormat('A4')
            ->setDisk('local');

        $this->assertInstanceOf(ExcelExporter::class, $result);
    }
}

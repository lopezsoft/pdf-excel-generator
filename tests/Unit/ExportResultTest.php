<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Tests\Unit;

use Lopezsoft\PdfExcelGenerator\Results\ExportResult;
use PHPUnit\Framework\TestCase;

/**
 * Test para ExportResult.
 */
class ExportResultTest extends TestCase
{
    private ExportResult $result;

    protected function setUp(): void
    {
        parent::setUp();
        $this->result = new ExportResult('test.pdf', 'local', 'exports/test.pdf');
    }

    public function test_returns_filename(): void
    {
        $this->assertEquals('test.pdf', $this->result->filename());
    }

    public function test_returns_disk(): void
    {
        $this->assertEquals('local', $this->result->disk());
    }

    public function test_get_mime_type_for_pdf(): void
    {
        $result = new ExportResult('document.pdf', 'local', 'document.pdf');
        $response = $result->download();

        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_get_mime_type_for_xlsx(): void
    {
        $result = new ExportResult('spreadsheet.xlsx', 'local', 'spreadsheet.xlsx');
        $response = $result->download();

        $this->assertEquals(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('Content-Type')
        );
    }

    public function test_download_with_custom_name(): void
    {
        $response = $this->result->download('custom-name.pdf');

        $this->assertStringContainsString(
            'attachment; filename="custom-name.pdf"',
            $response->headers->get('Content-Disposition')
        );
    }
}

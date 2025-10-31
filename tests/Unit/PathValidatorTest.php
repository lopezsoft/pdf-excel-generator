<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Tests\Unit;

use Lopezsoft\PdfExcelGenerator\Validators\PathValidator;
use Lopezsoft\PdfExcelGenerator\Exceptions\InvalidPathException;
use PHPUnit\Framework\TestCase;

/**
 * Test para PathValidator.
 */
class PathValidatorTest extends TestCase
{
    private PathValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new PathValidator();
    }

    public function test_validates_secure_path(): void
    {
        $this->assertTrue($this->validator->validateSecurePath('documents/invoice.pdf'));
        $this->assertTrue($this->validator->validateSecurePath('reports/2024/summary.xlsx'));
    }

    public function test_throws_exception_for_directory_traversal(): void
    {
        $this->expectException(InvalidPathException::class);
        $this->validator->validateSecurePath('../../../etc/passwd');
    }

    public function test_validates_pdf_extension(): void
    {
        $this->assertTrue($this->validator->validatePdfExtension('document.pdf'));
        $this->assertTrue($this->validator->validatePdfExtension('report.PDF'));
    }

    public function test_throws_exception_for_invalid_pdf_extension(): void
    {
        $this->expectException(InvalidPathException::class);
        $this->validator->validatePdfExtension('document.xlsx');
    }

    public function test_validates_excel_extension(): void
    {
        $this->assertTrue($this->validator->validateExcelExtension('data.xlsx'));
        $this->assertTrue($this->validator->validateExcelExtension('data.xls'));
        $this->assertTrue($this->validator->validateExcelExtension('data.csv'));
    }

    public function test_throws_exception_for_invalid_excel_extension(): void
    {
        $this->expectException(InvalidPathException::class);
        $this->validator->validateExcelExtension('data.pdf');
    }

    public function test_sanitizes_filename(): void
    {
        $this->assertEquals(
            'my_invoice.pdf',
            $this->validator->sanitizeFilename('my invoice.pdf')
        );

        $this->assertEquals(
            'report_2024.xlsx',
            $this->validator->sanitizeFilename('report@2024.xlsx')
        );

        $this->assertEquals(
            'document___test.pdf',
            $this->validator->sanitizeFilename('document!@#test.pdf')
        );
    }
}

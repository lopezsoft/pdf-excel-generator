<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade para PdfExcelGenerator.
 *
 * @method static \Lopezsoft\PdfExcelGenerator\PdfExcelGenerator html(string $html)
 * @method static \Lopezsoft\PdfExcelGenerator\PdfExcelGenerator blade(string $template, array $data = [])
 * @method static \Lopezsoft\PdfExcelGenerator\PdfExcelGenerator data(array $data)
 * @method static \Lopezsoft\PdfExcelGenerator\PdfExcelGenerator format(string $format)
 * @method static \Lopezsoft\PdfExcelGenerator\PdfExcelGenerator disk(string $disk)
 * @method static \Lopezsoft\PdfExcelGenerator\Contracts\ExportResultInterface savePdf(string $filename = 'output.pdf')
 * @method static \Lopezsoft\PdfExcelGenerator\Contracts\ExportResultInterface saveExcel(string $filename = 'output.xlsx')
 * @method static string streamPdf()
 * @method static string streamExcel()
 * @method static \Lopezsoft\PdfExcelGenerator\PdfExcelGenerator pdfOptions(array $options)
 * @method static \Lopezsoft\PdfExcelGenerator\PdfExcelGenerator sheetTitle(string $title)
 * @method static \Lopezsoft\PdfExcelGenerator\PdfExcelGenerator excelOptions(array $options)
 *
 * @see \Lopezsoft\PdfExcelGenerator\PdfExcelGenerator
 */
class PdfExcelGenerator extends Facade
{
    /**
     * Obtiene el nombre del binding en el contenedor.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'pdf-excel-generator';
    }
}

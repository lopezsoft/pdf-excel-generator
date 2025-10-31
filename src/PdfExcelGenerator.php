<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator;

use Lopezsoft\PdfExcelGenerator\Contracts\ExportResultInterface;
use Lopezsoft\PdfExcelGenerator\Exporters\PdfExporter;
use Lopezsoft\PdfExcelGenerator\Exporters\ExcelExporter;
use Lopezsoft\PdfExcelGenerator\Validators\TemplateValidator;
use Lopezsoft\PdfExcelGenerator\Exceptions\GeneratorException;

/**
 * Clase principal del generador de PDFs y Excel.
 *
 * Proporciona una API fluida para generar PDFs y archivos Excel
 * desde HTML, Blade templates o datos.
 */
class PdfExcelGenerator
{
    /**
     * Exportador de PDF.
     */
    private PdfExporter $pdfExporter;

    /**
     * Exportador de Excel.
     */
    private ExcelExporter $excelExporter;

    /**
     * Validador de templates.
     */
    private TemplateValidator $templateValidator;

    /**
     * Tipo de contenido actual.
     */
    private ?string $contentType = null;

    /**
     * Formato del documento.
     */
    private string $format = 'A4';

    /**
     * Disco de almacenamiento.
     */
    private string $disk;

    /**
     * Path de Chrome.
     */
    private ?string $chromePath = null;

    /**
     * Márgenes del PDF.
     *
     * @var array{top: int, right: int, bottom: int, left: int}
     */
    private array $margins = [
        'top' => 10,
        'right' => 10,
        'bottom' => 10,
        'left' => 10,
    ];

    /**
     * Constructor.
     *
     * @param array<string, mixed> $config Configuración del paquete
     */
    public function __construct(array $config = [])
    {
        $this->pdfExporter = new PdfExporter();
        $this->excelExporter = new ExcelExporter();
        $this->templateValidator = new TemplateValidator();

        // Configuración por defecto
        $this->disk = $config['disk'] ?? 'local';
        $this->format = $config['format'] ?? 'A4';
        $this->chromePath = $config['chrome_path'] ?? null;
    }

    /**
     * Establece contenido HTML para exportar.
     *
     * @param string $html Contenido HTML
     * @return self
     */
    public function html(string $html): self
    {
        $this->pdfExporter->setHtml($html);
        $this->contentType = 'html';
        return $this;
    }

    /**
     * Establece un template Blade para exportar.
     *
     * @param string $template Nombre del template (sin extensión .blade.php)
     * @param array<string, mixed> $data Datos para el template
     * @return self
     * @throws \Lopezsoft\PdfExcelGenerator\Exceptions\InvalidTemplateException
     */
    public function blade(string $template, array $data = []): self
    {
        $html = $this->templateValidator->renderTemplate($template, $data);
        $this->pdfExporter->setHtml($html);
        $this->contentType = 'blade';
        return $this;
    }

    /**
     * Establece datos para exportar a Excel.
     *
     * @param array<int, array<int|string, mixed>> $data Array bidimensional de datos
     * @return self
     */
    public function data(array $data): self
    {
        $this->excelExporter->setData($data);
        $this->contentType = 'data';
        return $this;
    }

    /**
     * Establece el formato del documento.
     *
     * @param string $format Formato (A4, letter, legal, etc.)
     * @return self
     */
    public function format(string $format): self
    {
        $this->format = $format;
        return $this;
    }

    /**
     * Establece el disco de almacenamiento.
     *
     * @param string $disk Nombre del disco configurado en config/filesystems.php
     * @return self
     */
    public function disk(string $disk): self
    {
        $this->disk = $disk;
        return $this;
    }

    /**
     * Establece los márgenes del PDF (todos iguales).
     *
     * @param int $margin Margen en milímetros para todos los lados
     * @return self
     */
    public function margins(int $margin): self
    {
        $this->margins = [
            'top' => $margin,
            'right' => $margin,
            'bottom' => $margin,
            'left' => $margin,
        ];
        return $this;
    }

    /**
     * Establece los márgenes del PDF individualmente.
     *
     * @param int $top Margen superior en milímetros
     * @param int $right Margen derecho en milímetros
     * @param int $bottom Margen inferior en milímetros
     * @param int $left Margen izquierdo en milímetros
     * @return self
     */
    public function customMargins(int $top, int $right, int $bottom, int $left): self
    {
        $this->margins = compact('top', 'right', 'bottom', 'left');
        return $this;
    }

    /**
     * Guarda como PDF y retorna el resultado.
     *
     * @param string $filename Nombre del archivo (con extensión .pdf)
     * @return ExportResultInterface
     * @throws GeneratorException
     */
    public function savePdf(string $filename = 'output.pdf'): ExportResultInterface
    {
        if ($this->contentType === null || $this->contentType === 'data') {
            throw GeneratorException::invalidContent('Cannot generate PDF from data. Use html() or blade() methods.');
        }

        $this->pdfExporter
            ->setFormat($this->format)
            ->setDisk($this->disk)
            ->setMargins($this->margins)
            ->setChromePath($this->chromePath);

        return $this->pdfExporter->save($filename);
    }

    /**
     * Guarda como Excel y retorna el resultado.
     *
     * @param string $filename Nombre del archivo (con extensión .xlsx, .xls, .csv)
     * @return ExportResultInterface
     * @throws GeneratorException
     */
    public function saveExcel(string $filename = 'output.xlsx'): ExportResultInterface
    {
        if ($this->contentType !== 'data') {
            throw GeneratorException::invalidContent('Cannot generate Excel from HTML. Use data() method.');
        }

        // Detectar tipo de writer basado en la extensión
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $this->excelExporter
            ->setWriterType($extension)
            ->setDisk($this->disk);

        return $this->excelExporter->save($filename);
    }

    /**
     * Genera y retorna el contenido del PDF como stream.
     *
     * @return string Contenido binario del PDF
     * @throws GeneratorException
     */
    public function streamPdf(): string
    {
        if ($this->contentType === null || $this->contentType === 'data') {
            throw GeneratorException::invalidContent('Cannot generate PDF from data. Use html() or blade() methods.');
        }

        $this->pdfExporter
            ->setFormat($this->format)
            ->setMargins($this->margins)
            ->setChromePath($this->chromePath);

        return $this->pdfExporter->stream();
    }

    /**
     * Genera y retorna el contenido del Excel como stream.
     *
     * @return string Contenido binario del Excel
     * @throws GeneratorException
     */
    public function streamExcel(): string
    {
        if ($this->contentType !== 'data') {
            throw GeneratorException::invalidContent('Cannot generate Excel from HTML. Use data() method.');
        }

        return $this->excelExporter->stream();
    }

    /**
     * Establece opciones adicionales para el exportador PDF.
     *
     * @param array<string, mixed> $options
     * @return self
     */
    public function pdfOptions(array $options): self
    {
        $this->pdfExporter->setOptions($options);
        return $this;
    }

    /**
     * Establece el título de la hoja de Excel.
     *
     * @param string $title
     * @return self
     */
    public function sheetTitle(string $title): self
    {
        $this->excelExporter->setSheetTitle($title);
        return $this;
    }

    /**
     * Establece opciones del writer de Excel.
     *
     * @param array<string, mixed> $options
     * @return self
     */
    public function excelOptions(array $options): self
    {
        $this->excelExporter->setWriterOptions($options);
        return $this;
    }
}

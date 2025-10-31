<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Exporters;

use Spatie\Browsershot\Browsershot;
use Lopezsoft\PdfExcelGenerator\Exceptions\ChromeNotFoundException;
use Lopezsoft\PdfExcelGenerator\Exceptions\ExportException;
use Lopezsoft\PdfExcelGenerator\Exceptions\InvalidPdfException;

/**
 * Exportador de PDFs usando spatie/browsershot.
 *
 * Convierte HTML a PDF usando Puppeteer/Chrome.
 */
class PdfExporter extends AbstractExporter
{
    /**
     * Path personalizado de Chrome.
     */
    private ?string $chromePath = null;

    /**
     * Opciones adicionales de Browsershot.
     *
     * @var array<string, mixed>
     */
    private array $options = [];

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
     * Imprimir backgrounds (colores de fondo e imágenes de fondo).
     */
    private bool $printBackground = true;

    /**
     * Establece el path de Chrome.
     *
     * @param string|null $path
     * @return self
     */
    public function setChromePath(?string $path): self
    {
        $this->chromePath = $path;
        return $this;
    }

    /**
     * Establece opciones adicionales de Browsershot.
     *
     * @param array<string, mixed> $options
     * @return self
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Establece los márgenes del PDF.
     *
     * @param array{top: int, right: int, bottom: int, left: int} $margins
     * @return self
     */
    public function setMargins(array $margins): self
    {
        $this->margins = $margins;
        return $this;
    }

    /**
     * Habilita o deshabilita la impresión de backgrounds (colores e imágenes de fondo).
     *
     * Por defecto está habilitado (true) para que los colores de fondo se rendericen.
     * Si se deshabilita (false), los fondos se omiten (comportamiento clásico de impresión).
     *
     * @param bool $print
     * @return self
     */
    public function printBackground(bool $print = true): self
    {
        $this->printBackground = $print;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function validate(): bool
    {
        return !empty($this->html);
    }

    /**
     * {@inheritDoc}
     */
    protected function getMissingDataType(): string
    {
        return 'html';
    }

    /**
     * {@inheritDoc}
     */
    protected function generate(): string
    {
        try {
            // Normalizar HTML antes de renderizar (reduce tamaño y espacios excesivos)
            $normalizedHtml = $this->normalizeHtml($this->html);
            
            $browsershot = Browsershot::html($normalizedHtml)
                ->format($this->format)
                ->margins(
                    $this->margins['top'],
                    $this->margins['right'],
                    $this->margins['bottom'],
                    $this->margins['left']
                )
                ->showBackground($this->printBackground)
                ->noSandbox() // Requerido para servidores Linux
                ->setOption('args', ['--disable-dev-shm-usage']); // Previene errores de memoria compartida

            // Configurar Chrome path: usar el especificado manualmente, o el de configuración, o detección automática
            $chromePath = $this->chromePath ?? config('pdf-excel-generator.chrome_path');
            
            if ($chromePath !== null) {
                if (!file_exists($chromePath)) {
                    throw ChromeNotFoundException::invalidPath($chromePath);
                }
                $browsershot->setChromePath($chromePath);
            }

            // Aplicar opciones adicionales
            foreach ($this->options as $method => $value) {
                if (method_exists($browsershot, $method)) {
                    $browsershot->{$method}($value);
                }
            }

            // En Windows, pdf() tiene problemas de encoding.
            // Usamos archivo temporal para garantizar integridad
            $tempFile = tempnam(sys_get_temp_dir(), 'pdf_') . '.pdf';
            
            try {
                $browsershot->save($tempFile);
                $content = file_get_contents($tempFile);
                
                if ($content === false) {
                    throw ExportException::streamFailed('Failed to read temporary PDF file');
                }
                
                return $content;
            } finally {
                if (file_exists($tempFile)) {
                    @unlink($tempFile);
                }
            }
        } catch (ChromeNotFoundException $e) {
            throw $e;
        } catch (\Throwable $e) {
            // Si Chrome no está instalado, Browsershot lanzará una excepción
            if (str_contains($e->getMessage(), 'chrome') || str_contains($e->getMessage(), 'chromium')) {
                throw ChromeNotFoundException::notInstalled();
            }

            throw ExportException::streamFailed($e->getMessage());
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function validateBeforeSave(): void
    {
        parent::validateBeforeSave();
        $this->pathValidator->validatePdfExtension($this->getFilenameFromContext());
    }

    /**
     * Obtiene el filename del contexto actual (helper temporal).
     *
     * @return string
     */
    private function getFilenameFromContext(): string
    {
        // Este método será llamado desde save() que ya tiene el filename
        // Por ahora retornamos un default para validación
        return 'output.pdf';
    }

    /**
     * Normaliza HTML eliminando espacios innecesarios.
     *
     * Puppeteer renderiza HTML más literalmente que mPDF, causando espacios excesivos
     * por la indentación de Blade. Esta normalización reduce tamaño y mejora renderizado.
     *
     * @param string $html HTML original
     * @return string HTML normalizado
     */
    private function normalizeHtml(string $html): string
    {
        // Remover espacios entre tags (Blade genera indentación excesiva)
        $html = preg_replace('/>\s+</', '><', $html);
        
        // Normalizar espacios múltiples a uno solo
        $html = preg_replace('/\s+/u', ' ', $html);
        
        // Remover espacio antes de tags de apertura/cierre
        $html = preg_replace('/ <(?=\w|\/)/u', '<', $html);
        
        return $html;
    }

    /**
     * {@inheritDoc}
     *
     * Valida que el PDF generado sea válido verificando su header.
     *
     * @param string $content
     * @throws InvalidPdfException
     */
    protected function validateGeneratedContent(string $content): void
    {
        parent::validateGeneratedContent($content);

        // Validar header PDF (%PDF-x.x)
        if (substr($content, 0, 4) !== '%PDF') {
            throw InvalidPdfException::invalidHeader($content);
        }

        // Validar tamaño mínimo (un PDF válido tiene al menos 100 bytes)
        if (strlen($content) < 100) {
            throw InvalidPdfException::invalidSize(strlen($content));
        }
    }
}

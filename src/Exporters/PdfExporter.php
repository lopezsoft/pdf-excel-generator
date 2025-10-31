<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Exporters;

use Spatie\Browsershot\Browsershot;
use Lopezsoft\PdfExcelGenerator\Exceptions\ChromeNotFoundException;
use Lopezsoft\PdfExcelGenerator\Exceptions\ExportException;

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
            $browsershot = Browsershot::html($this->html)
                ->format($this->format)
                ->margins(10, 10, 10, 10);

            // Configurar Chrome path si está especificado
            if ($this->chromePath !== null) {
                if (!file_exists($this->chromePath)) {
                    throw ChromeNotFoundException::invalidPath($this->chromePath);
                }
                $browsershot->setChromePath($this->chromePath);
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
}

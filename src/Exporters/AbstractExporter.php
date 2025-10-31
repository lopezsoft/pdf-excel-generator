<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Exporters;

use Illuminate\Support\Facades\Storage;
use Lopezsoft\PdfExcelGenerator\Contracts\ExporterInterface;
use Lopezsoft\PdfExcelGenerator\Contracts\ExportResultInterface;
use Lopezsoft\PdfExcelGenerator\Exceptions\ExportException;
use Lopezsoft\PdfExcelGenerator\Results\ExportResult;
use Lopezsoft\PdfExcelGenerator\Validators\PathValidator;

/**
 * Clase abstracta base para exportadores.
 *
 * Implementa Template Method Pattern proporcionando lógica común
 * para todos los exportadores (PDF, Excel).
 */
abstract class AbstractExporter implements ExporterInterface
{
    /**
     * Contenido HTML a exportar.
     */
    protected ?string $html = null;

    /**
     * Datos para exportar (usado principalmente en Excel).
     *
     * @var array<int, array<int|string, mixed>>|null
     */
    protected ?array $data = null;

    /**
     * Formato del documento (A4, letter, etc.).
     */
    protected string $format = 'A4';

    /**
     * Disco de almacenamiento.
     */
    protected string $disk = 'local';

    /**
     * Validador de rutas.
     */
    protected PathValidator $pathValidator;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->pathValidator = new PathValidator();
    }

    /**
     * {@inheritDoc}
     */
    public function setHtml(string $html): self
    {
        $this->html = $html;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setFormat(string $format): self
    {
        $this->format = $format;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setDisk(string $disk): self
    {
        // Validar que el disco existe
        if (!array_key_exists($disk, config('filesystems.disks', []))) {
            throw ExportException::invalidDisk($disk);
        }

        $this->disk = $disk;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function save(string $filename): ExportResultInterface
    {
        $this->validateBeforeSave();

        // Sanitizar nombre de archivo
        $sanitizedFilename = $this->pathValidator->sanitizeFilename($filename);

        // Validar seguridad de la ruta
        $this->pathValidator->validateSecurePath($sanitizedFilename);

        // Generar el contenido
        $content = $this->generate();

        // Guardar en el disco
        $saved = Storage::disk($this->disk)->put($sanitizedFilename, $content);

        if (!$saved) {
            throw ExportException::saveFailed($sanitizedFilename, 'Failed to write file to disk');
        }

        return new ExportResult($sanitizedFilename, $this->disk, $sanitizedFilename);
    }

    /**
     * {@inheritDoc}
     */
    public function stream(): string
    {
        $this->validateBeforeStream();

        return $this->generate();
    }

    /**
     * Genera el contenido del archivo.
     *
     * Template Method: debe ser implementado por las clases hijas.
     *
     * @return string Contenido binario del archivo
     * @throws ExportException
     */
    abstract protected function generate(): string;

    /**
     * Valida antes de guardar.
     *
     * Template Method: puede ser sobrescrito por las clases hijas.
     *
     * @throws ExportException
     */
    protected function validateBeforeSave(): void
    {
        if (!$this->validate()) {
            throw ExportException::missingData($this->getMissingDataType());
        }
    }

    /**
     * Valida antes de hacer stream.
     *
     * Template Method: puede ser sobrescrito por las clases hijas.
     *
     * @throws ExportException
     */
    protected function validateBeforeStream(): void
    {
        $this->validateBeforeSave();
    }

    /**
     * Obtiene el tipo de dato faltante.
     *
     * @return string
     */
    abstract protected function getMissingDataType(): string;
}

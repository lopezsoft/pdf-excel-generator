<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Results;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Lopezsoft\PdfExcelGenerator\Contracts\ExportResultInterface;

/**
 * Value Object que representa el resultado de una exportación.
 *
 * Encapsula toda la información sobre un archivo exportado y proporciona
 * métodos convenientes para acceder a él.
 */
class ExportResult implements ExportResultInterface
{
    /**
     * Nombre del archivo.
     */
    private string $filename;

    /**
     * Disco de almacenamiento.
     */
    private string $disk;

    /**
     * Ruta relativa del archivo en el disco.
     */
    private string $relativePath;

    /**
     * Contenido binario del archivo (lazy loaded).
     */
    private ?string $content = null;

    /**
     * Constructor.
     *
     * @param string $filename Nombre del archivo
     * @param string $disk Nombre del disco de almacenamiento
     * @param string $relativePath Ruta relativa en el disco
     */
    public function __construct(string $filename, string $disk, string $relativePath)
    {
        $this->filename = $filename;
        $this->disk = $disk;
        $this->relativePath = $relativePath;
    }

    /**
     * {@inheritDoc}
     */
    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->relativePath);
    }

    /**
     * {@inheritDoc}
     */
    public function path(): string
    {
        return Storage::disk($this->disk)->path($this->relativePath);
    }

    /**
     * {@inheritDoc}
     */
    public function stream(): string
    {
        if ($this->content === null) {
            $this->content = Storage::disk($this->disk)->get($this->relativePath);
        }

        return $this->content;
    }

    /**
     * {@inheritDoc}
     */
    public function download(?string $downloadName = null): Response
    {
        $name = $downloadName ?? $this->filename;
        $content = $this->stream();
        $mimeType = $this->getMimeType();

        return response($content, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => "attachment; filename=\"{$name}\"",
            'Content-Length' => strlen($content),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function filename(): string
    {
        return $this->filename;
    }

    /**
     * {@inheritDoc}
     */
    public function disk(): string
    {
        return $this->disk;
    }

    /**
     * Obtiene el MIME type del archivo basándose en su extensión.
     *
     * @return string
     */
    private function getMimeType(): string
    {
        $extension = strtolower(pathinfo($this->filename, PATHINFO_EXTENSION));

        return match ($extension) {
            'pdf' => 'application/pdf',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls' => 'application/vnd.ms-excel',
            'csv' => 'text/csv',
            default => 'application/octet-stream',
        };
    }

    /**
     * Verifica si el archivo existe en el disco.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return Storage::disk($this->disk)->exists($this->relativePath);
    }

    /**
     * Elimina el archivo del disco.
     *
     * @return bool
     */
    public function delete(): bool
    {
        return Storage::disk($this->disk)->delete($this->relativePath);
    }
}

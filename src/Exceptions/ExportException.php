<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Exceptions;

/**
 * Excepción lanzada cuando ocurre un error durante la exportación.
 */
class ExportException extends GeneratorException
{
    /**
     * Crea una excepción para error al guardar archivo.
     *
     * @param string $filename
     * @param string $reason
     * @return static
     */
    public static function saveFailed(string $filename, string $reason = ''): static
    {
        $message = "Failed to save file: {$filename}";
        if ($reason) {
            $message .= ". Reason: {$reason}";
        }
        return new static($message, 500);
    }

    /**
     * Crea una excepción para error al generar stream.
     *
     * @param string $reason
     * @return static
     */
    public static function streamFailed(string $reason = ''): static
    {
        $message = 'Failed to generate stream';
        if ($reason) {
            $message .= ": {$reason}";
        }
        return new static($message, 500);
    }

    /**
     * Crea una excepción para datos faltantes.
     *
     * @param string $type Tipo de dato faltante (html, data, etc.)
     * @return static
     */
    public static function missingData(string $type): static
    {
        return new static("Missing required data: {$type}. Please provide content before exporting.", 400);
    }

    /**
     * Crea una excepción para disco de storage inválido.
     *
     * @param string $disk
     * @return static
     */
    public static function invalidDisk(string $disk): static
    {
        return new static("Invalid storage disk: {$disk}. Make sure the disk is configured in config/filesystems.php", 400);
    }
}

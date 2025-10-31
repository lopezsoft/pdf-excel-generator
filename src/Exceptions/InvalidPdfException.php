<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Exceptions;

/**
 * Excepción lanzada cuando se detecta un PDF corrupto o inválido.
 */
class InvalidPdfException extends ExportException
{
    /**
     * Crea excepción por PDF corrupto.
     *
     * @param string $reason Razón de la corrupción
     * @return self
     */
    public static function corrupted(string $reason = 'Generated file is not a valid PDF'): self
    {
        return new self($reason);
    }

    /**
     * Crea excepción por header inválido.
     *
     * @param string $actualHeader Header detectado
     * @return self
     */
    public static function invalidHeader(string $actualHeader): self
    {
        $displayHeader = bin2hex(substr($actualHeader, 0, 10));
        return new self(
            "Generated PDF has invalid header. Expected '%PDF', got hex: {$displayHeader}"
        );
    }

    /**
     * Crea excepción por tamaño inválido.
     *
     * @param int $size Tamaño en bytes
     * @return self
     */
    public static function invalidSize(int $size): self
    {
        return new self("Generated PDF is too small ({$size} bytes), likely corrupted");
    }
}

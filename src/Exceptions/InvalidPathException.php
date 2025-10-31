<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Exceptions;

/**
 * Excepción lanzada cuando una ruta de archivo es insegura.
 *
 * Previene ataques de directory traversal.
 */
class InvalidPathException extends GeneratorException
{
    /**
     * Crea una excepción para path inseguro (directory traversal).
     *
     * @param string $path
     * @return static
     */
    public static function directoryTraversal(string $path): static
    {
        return new static(
            "Insecure file path detected: {$path}. Path traversal is not allowed for security reasons.",
            403
        );
    }

    /**
     * Crea una excepción para extensión de archivo no permitida.
     *
     * @param string $extension
     * @param array<int, string> $allowed
     * @return static
     */
    public static function invalidExtension(string $extension, array $allowed): static
    {
        $allowedStr = implode(', ', $allowed);
        return new static(
            "Invalid file extension: {$extension}. Allowed extensions: {$allowedStr}",
            400
        );
    }
}

<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Validators;

use Lopezsoft\PdfExcelGenerator\Exceptions\InvalidPathException;

/**
 * Validador de rutas de archivos.
 *
 * Previene ataques de directory traversal y valida extensiones permitidas.
 * Implementa el principio de Single Responsibility.
 */
class PathValidator
{
    /**
     * Extensiones permitidas para PDFs.
     */
    private const PDF_EXTENSIONS = ['pdf'];

    /**
     * Extensiones permitidas para Excel.
     */
    private const EXCEL_EXTENSIONS = ['xlsx', 'xls', 'csv'];

    /**
     * Valida que una ruta sea segura (sin directory traversal).
     *
     * @param string $path Ruta a validar
     * @return bool
     * @throws InvalidPathException
     */
    public function validateSecurePath(string $path): bool
    {
        // Normalizar la ruta
        $normalizedPath = str_replace('\\', '/', $path);

        // Verificar que no contenga secuencias de directory traversal
        if (
            str_contains($normalizedPath, '../') ||
            str_contains($normalizedPath, '..\\') ||
            str_contains($path, "\0")
        ) {
            throw InvalidPathException::directoryTraversal($path);
        }

        return true;
    }

    /**
     * Valida que la extensión del archivo sea permitida para PDFs.
     *
     * @param string $filename Nombre del archivo
     * @return bool
     * @throws InvalidPathException
     */
    public function validatePdfExtension(string $filename): bool
    {
        return $this->validateExtension($filename, self::PDF_EXTENSIONS);
    }

    /**
     * Valida que la extensión del archivo sea permitida para Excel.
     *
     * @param string $filename Nombre del archivo
     * @return bool
     * @throws InvalidPathException
     */
    public function validateExcelExtension(string $filename): bool
    {
        return $this->validateExtension($filename, self::EXCEL_EXTENSIONS);
    }

    /**
     * Valida que la extensión del archivo esté en la lista de permitidas.
     *
     * @param string $filename Nombre del archivo
     * @param array<int, string> $allowedExtensions Extensiones permitidas
     * @return bool
     * @throws InvalidPathException
     */
    private function validateExtension(string $filename, array $allowedExtensions): bool
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions, true)) {
            throw InvalidPathException::invalidExtension($extension, $allowedExtensions);
        }

        return true;
    }

    /**
     * Sanitiza un nombre de archivo removiendo caracteres peligrosos.
     * Preserva la estructura de directorios en el path.
     *
     * @param string $filename Nombre del archivo (puede incluir subdirectorios)
     * @return string Nombre sanitizado con directorios preservados
     */
    public function sanitizeFilename(string $filename): string
    {
        // Normalizar separadores de ruta
        $normalized = str_replace('\\', '/', $filename);
        
        // Separar directorio y archivo
        $directory = dirname($normalized);
        $basename = basename($normalized);
        
        // Sanitizar solo el basename (no el directorio)
        $pathinfo = pathinfo($basename);
        $name = $pathinfo['filename'] ?? '';
        $extension = $pathinfo['extension'] ?? '';

        // Permitir solo caracteres alfanuméricos, guiones, underscores y puntos en el nombre
        $name = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);
        
        $sanitizedBasename = $extension ? "{$name}.{$extension}" : $name;
        
        // Reconstruir el path completo
        if ($directory === '.' || $directory === '') {
            return $sanitizedBasename;
        }
        
        return "{$directory}/{$sanitizedBasename}";
    }
}

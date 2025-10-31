<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Exceptions;

/**
 * Excepción lanzada cuando Chrome/Chromium no está instalado o no se encuentra.
 */
class ChromeNotFoundException extends GeneratorException
{
    /**
     * Crea una excepción para Chrome no encontrado.
     *
     * @return static
     */
    public static function notInstalled(): static
    {
        return new static(
            'Chrome/Chromium not found. Please install Chrome or Chromium and ensure it\'s in your PATH, ' .
            'or configure the chrome_path in config/pdf-excel-generator.php',
            500
        );
    }

    /**
     * Crea una excepción para Chrome path inválido.
     *
     * @param string $path
     * @return static
     */
    public static function invalidPath(string $path): static
    {
        return new static("Chrome executable not found at path: {$path}. Please verify the chrome_path configuration.", 500);
    }
}

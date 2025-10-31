<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Exceptions;

use Exception;

/**
 * Excepción base para el generador de PDFs y Excel.
 *
 * Todas las excepciones del paquete deben heredar de esta clase.
 */
class GeneratorException extends Exception
{
    /**
     * Código de error por defecto.
     */
    protected $code = 500;

    /**
     * Crea una excepción para contenido inválido.
     *
     * @param string $message
     * @return static
     */
    public static function invalidContent(string $message = 'Invalid content provided'): static
    {
        return new static($message, 400);
    }

    /**
     * Crea una excepción para configuración inválida.
     *
     * @param string $key Clave de configuración
     * @return static
     */
    public static function invalidConfiguration(string $key): static
    {
        return new static("Invalid configuration for key: {$key}", 500);
    }
}

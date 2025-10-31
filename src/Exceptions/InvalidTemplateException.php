<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Exceptions;

/**
 * Excepción lanzada cuando un template Blade no existe o es inválido.
 */
class InvalidTemplateException extends GeneratorException
{
    /**
     * Crea una excepción para template no encontrado.
     *
     * @param string $template Nombre del template
     * @return static
     */
    public static function notFound(string $template): static
    {
        return new static("Blade template not found: {$template}. Please check the template path and name.", 404);
    }

    /**
     * Crea una excepción para error al renderizar template.
     *
     * @param string $template
     * @param string $reason
     * @return static
     */
    public static function renderFailed(string $template, string $reason = ''): static
    {
        $message = "Failed to render Blade template: {$template}";
        if ($reason) {
            $message .= ". Reason: {$reason}";
        }
        return new static($message, 500);
    }
}

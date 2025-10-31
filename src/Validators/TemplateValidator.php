<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Validators;

use Illuminate\Support\Facades\View;
use Lopezsoft\PdfExcelGenerator\Exceptions\InvalidTemplateException;

/**
 * Validador de templates Blade.
 *
 * Verifica la existencia y validez de templates Blade antes de renderizar.
 */
class TemplateValidator
{
    /**
     * Valida que un template Blade exista.
     *
     * @param string $template Nombre del template (sin extensión .blade.php)
     * @return bool
     * @throws InvalidTemplateException
     */
    public function validateTemplateExists(string $template): bool
    {
        if (!View::exists($template)) {
            throw InvalidTemplateException::notFound($template);
        }

        return true;
    }

    /**
     * Valida y renderiza un template Blade con datos.
     *
     * @param string $template Nombre del template
     * @param array<string, mixed> $data Datos para el template
     * @return string HTML renderizado
     * @throws InvalidTemplateException
     */
    public function renderTemplate(string $template, array $data = []): string
    {
        $this->validateTemplateExists($template);

        try {
            return View::make($template, $data)->render();
        } catch (\Throwable $e) {
            throw InvalidTemplateException::renderFailed($template, $e->getMessage());
        }
    }
}

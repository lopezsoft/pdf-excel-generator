<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator;

use Illuminate\Support\ServiceProvider;

/**
 * Service Provider para el paquete pdf-excel-generator.
 *
 * Registra el generador en el contenedor de Laravel y publica la configuración.
 */
class PdfExcelGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Registra los servicios del paquete.
     *
     * @return void
     */
    public function register(): void
    {
        // Combinar configuración
        $this->mergeConfigFrom(
            __DIR__ . '/../config/pdf-excel-generator.php',
            'pdf-excel-generator'
        );

        // Registrar el generador en el contenedor
        $this->app->singleton('pdf-excel-generator', function ($app) {
            return new PdfExcelGenerator(config('pdf-excel-generator', []));
        });

        // Alias para facilitar la inyección de dependencias
        $this->app->alias('pdf-excel-generator', PdfExcelGenerator::class);
    }

    /**
     * Bootstrap de los servicios del paquete.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publicar configuración
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/pdf-excel-generator.php' => config_path('pdf-excel-generator.php'),
            ], 'pdf-excel-generator-config');
        }
    }

    /**
     * Obtiene los servicios proporcionados por el provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            'pdf-excel-generator',
            PdfExcelGenerator::class,
        ];
    }
}

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
        $configPath = config('pdf-excel-generator.chrome_path');
        $envPath = env('CHROME_PATH');
        
        $message = 'Chrome/Chromium not found. ' . PHP_EOL;
        $message .= 'Current configuration: ' . PHP_EOL;
        $message .= '  - CHROME_PATH (env): ' . ($envPath ?: 'not set') . PHP_EOL;
        $message .= '  - chrome_path (config): ' . ($configPath ?: 'not set') . PHP_EOL . PHP_EOL;
        $message .= 'Solutions:' . PHP_EOL;
        $message .= '  1. Install Chrome/Chromium:' . PHP_EOL;
        $message .= '     Ubuntu/Debian: sudo apt-get install google-chrome-stable' . PHP_EOL;
        $message .= '     Or: sudo apt-get install chromium-browser' . PHP_EOL;
        $message .= '  2. Set CHROME_PATH in .env:' . PHP_EOL;
        $message .= '     CHROME_PATH=/usr/bin/google-chrome' . PHP_EOL;
        $message .= '  3. Or configure chrome_path in config/pdf-excel-generator.php';
        
        return new static($message, 500);
    }

    /**
     * Crea una excepción para Chrome path inválido.
     *
     * @param string $path
     * @return static
     */
    public static function invalidPath(string $path): static
    {
        $message = "Chrome executable not found at path: {$path}" . PHP_EOL;
        $message .= 'Please verify:' . PHP_EOL;
        $message .= '  1. The file exists: ls -la ' . $path . PHP_EOL;
        $message .= '  2. The file is executable: chmod +x ' . $path . PHP_EOL;
        $message .= '  3. Update CHROME_PATH in .env with the correct path' . PHP_EOL;
        $message .= '  4. Common paths:' . PHP_EOL;
        $message .= '     - /usr/bin/google-chrome' . PHP_EOL;
        $message .= '     - /usr/bin/chromium-browser' . PHP_EOL;
        $message .= '     - /usr/bin/chromium';
        
        return new static($message, 500);
    }
}

<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Services;

use Lopezsoft\PdfExcelGenerator\Contracts\ChromePoolInterface;
use Lopezsoft\PdfExcelGenerator\Exceptions\ChromeNotFoundException;
use Symfony\Component\Process\Process;

/**
 * Implementación del pool de Chrome para reutilización de instancias.
 *
 * VENTAJA: Reduce tiempo de generación de PDFs:
 * - Sin pool: ~4s por PDF (lanza Chrome cada vez)
 * - Con pool: ~1.5s por PDF (reutiliza instancia existente)
 *
 * USO:
 * ```php
 * use Lopezsoft\PdfExcelGenerator\Services\ChromePool;
 *
 * // Iniciar pool (una vez al inicio de la aplicación)
 * ChromePool::getInstance()->start();
 *
 * // Usar normalmente el generador (automáticamente usará el pool)
 * $generator->html($html)->savePdf('output.pdf');
 *
 * // Detener pool (al finalizar la aplicación)
 * ChromePool::getInstance()->stop();
 * ```
 *
 * IMPORTANTE: El pool es opcional. Si no está activo, el generador
 * funcionará normalmente (modo standalone).
 */
class ChromePool implements ChromePoolInterface
{
    /**
     * Instancia singleton.
     */
    private static ?self $instance = null;

    /**
     * WebSocket endpoint de Chrome.
     */
    private ?string $wsEndpoint = null;

    /**
     * Proceso de Chrome en background.
     */
    private ?Process $chromeProcess = null;

    /**
     * Path de Chrome.
     */
    private ?string $chromePath = null;

    /**
     * Constructor privado (Singleton).
     */
    private function __construct()
    {
        // Singleton
    }

    /**
     * Obtiene la instancia singleton.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * {@inheritDoc}
     */
    public function getEndpoint(): string
    {
        if ($this->wsEndpoint === null) {
            throw new ChromeNotFoundException('Chrome pool is not started. Call start() first.');
        }

        // Verificar que Chrome sigue vivo
        if ($this->chromeProcess !== null && !$this->chromeProcess->isRunning()) {
            // Chrome crasheó, reiniciar
            $this->restart();
        }

        return $this->wsEndpoint;
    }

    /**
     * {@inheritDoc}
     */
    public function isActive(): bool
    {
        return $this->wsEndpoint !== null 
            && $this->chromeProcess !== null 
            && $this->chromeProcess->isRunning();
    }

    /**
     * {@inheritDoc}
     */
    public function start(?string $chromePath = null): void
    {
        if ($this->isActive()) {
            return; // Ya está iniciado
        }

        $this->chromePath = $chromePath ?? $this->detectChromePath();

        if (!file_exists($this->chromePath)) {
            throw ChromeNotFoundException::invalidPath($this->chromePath);
        }

        // Iniciar Chrome en modo headless con remote debugging
        $debugPort = $this->findAvailablePort();
        
        $command = [
            $this->chromePath,
            '--headless',
            '--disable-gpu',
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            "--remote-debugging-port={$debugPort}",
        ];

        $this->chromeProcess = new Process($command);
        $this->chromeProcess->start();

        // Esperar a que Chrome esté listo
        sleep(2);

        // Obtener WebSocket endpoint
        $this->wsEndpoint = $this->fetchWebSocketEndpoint($debugPort);

        if ($this->wsEndpoint === null) {
            $this->stop();
            throw new ChromeNotFoundException('Failed to start Chrome pool');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function stop(): void
    {
        if ($this->chromeProcess !== null) {
            $this->chromeProcess->stop();
            $this->chromeProcess = null;
        }

        $this->wsEndpoint = null;
    }

    /**
     * {@inheritDoc}
     */
    public function restart(): void
    {
        $this->stop();
        $this->start($this->chromePath);
    }

    /**
     * Detecta automáticamente el path de Chrome.
     *
     * @return string
     * @throws ChromeNotFoundException
     */
    private function detectChromePath(): string
    {
        // Intentar leer desde variable de entorno
        $envPath = getenv('CHROME_PATH');
        if ($envPath !== false && file_exists($envPath)) {
            return $envPath;
        }

        // Detección según OS
        if (PHP_OS_FAMILY === 'Windows') {
            $possiblePaths = [
                'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
                'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
            ];
        } elseif (PHP_OS_FAMILY === 'Linux') {
            $possiblePaths = [
                '/usr/bin/google-chrome',
                '/usr/bin/chromium-browser',
                '/usr/bin/chromium',
            ];
        } elseif (PHP_OS_FAMILY === 'Darwin') {
            $possiblePaths = [
                '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
            ];
        } else {
            throw ChromeNotFoundException::notInstalled();
        }

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        throw ChromeNotFoundException::notInstalled();
    }

    /**
     * Encuentra un puerto disponible para debugging.
     *
     * @return int
     */
    private function findAvailablePort(): int
    {
        // Intentar puerto por defecto primero
        $port = 9222;

        // Verificar si está disponible
        $socket = @fsockopen('127.0.0.1', $port, $errno, $errstr, 1);
        if ($socket === false) {
            return $port; // Puerto disponible
        }

        fclose($socket);

        // Buscar puerto aleatorio
        for ($i = 0; $i < 10; $i++) {
            $port = rand(9223, 9999);
            $socket = @fsockopen('127.0.0.1', $port, $errno, $errstr, 1);
            if ($socket === false) {
                return $port;
            }
            fclose($socket);
        }

        return 9222; // Fallback
    }

    /**
     * Obtiene el WebSocket endpoint de Chrome.
     *
     * @param int $debugPort
     * @return string|null
     */
    private function fetchWebSocketEndpoint(int $debugPort): ?string
    {
        $url = "http://127.0.0.1:{$debugPort}/json/version";

        // Intentar obtener endpoint (con reintentos)
        for ($i = 0; $i < 5; $i++) {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 2,
                    'ignore_errors' => true,
                ],
            ]);

            $response = @file_get_contents($url, false, $context);

            if ($response !== false) {
                $data = json_decode($response, true);
                if (isset($data['webSocketDebuggerUrl'])) {
                    return $data['webSocketDebuggerUrl'];
                }
            }

            sleep(1); // Esperar antes de reintentar
        }

        return null;
    }

    /**
     * Prevenir clonación (Singleton).
     */
    private function __clone()
    {
        // No clonable
    }

    /**
     * Prevenir deserialización (Singleton).
     */
    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize singleton');
    }
}

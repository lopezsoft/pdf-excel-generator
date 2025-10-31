<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Contracts;

/**
 * Interfaz para pool de instancias de Chrome.
 *
 * Permite reutilizar instancias de Chrome/Chromium para reducir
 * el tiempo de generación de PDFs de ~4s a ~1.5s.
 */
interface ChromePoolInterface
{
    /**
     * Obtiene el endpoint WebSocket de Chrome.
     *
     * Si no existe una instancia activa, la crea.
     *
     * @return string WebSocket endpoint (ws://...)
     * @throws \Lopezsoft\PdfExcelGenerator\Exceptions\ChromeNotFoundException
     */
    public function getEndpoint(): string;

    /**
     * Verifica si el pool está activo.
     *
     * @return bool
     */
    public function isActive(): bool;

    /**
     * Inicia el pool de Chrome.
     *
     * @param string|null $chromePath Path personalizado de Chrome
     * @return void
     * @throws \Lopezsoft\PdfExcelGenerator\Exceptions\ChromeNotFoundException
     */
    public function start(?string $chromePath = null): void;

    /**
     * Detiene el pool y cierra Chrome.
     *
     * @return void
     */
    public function stop(): void;

    /**
     * Reinicia el pool (útil si Chrome crashea).
     *
     * @return void
     */
    public function restart(): void;
}

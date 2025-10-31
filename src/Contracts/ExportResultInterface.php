<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Contracts;

use Illuminate\Http\Response;

/**
 * Interface para el resultado de una exportación.
 *
 * Value Object que encapsula el resultado de una operación de exportación.
 */
interface ExportResultInterface
{
    /**
     * Obtiene la URL descargable del archivo.
     *
     * @return string URL pública del archivo
     */
    public function url(): string;

    /**
     * Obtiene la ruta absoluta del archivo.
     *
     * @return string Ruta absoluta en el sistema de archivos
     */
    public function path(): string;

    /**
     * Obtiene el contenido binario del archivo.
     *
     * @return string Contenido binario
     */
    public function stream(): string;

    /**
     * Genera una respuesta HTTP para descargar el archivo.
     *
     * @param string|null $downloadName Nombre personalizado para la descarga
     * @return Response
     */
    public function download(?string $downloadName = null): Response;

    /**
     * Obtiene el nombre del archivo.
     *
     * @return string
     */
    public function filename(): string;

    /**
     * Obtiene el disco donde se guardó el archivo.
     *
     * @return string
     */
    public function disk(): string;
}

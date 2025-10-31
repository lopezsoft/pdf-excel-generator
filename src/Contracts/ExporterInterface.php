<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Contracts;

/**
 * Interface para exportadores de PDF y Excel.
 *
 * Define el contrato que deben cumplir todos los exportadores
 * siguiendo el patrón Strategy y el principio de segregación de interfaces.
 */
interface ExporterInterface
{
    /**
     * Establece el contenido HTML a exportar.
     *
     * @param string $html Contenido HTML
     * @return self
     */
    public function setHtml(string $html): self;

    /**
     * Establece los datos a exportar (para Excel).
     *
     * @param array<int, array<int|string, mixed>> $data Array bidimensional de datos
     * @return self
     */
    public function setData(array $data): self;

    /**
     * Establece el formato del documento.
     *
     * @param string $format Formato (A4, letter, etc.)
     * @return self
     */
    public function setFormat(string $format): self;

    /**
     * Establece el disco de almacenamiento.
     *
     * @param string $disk Nombre del disco
     * @return self
     */
    public function setDisk(string $disk): self;

    /**
     * Exporta y guarda el archivo en el disco.
     *
     * @param string $filename Nombre del archivo
     * @return \Lopezsoft\PdfExcelGenerator\Contracts\ExportResultInterface
     * @throws \Lopezsoft\PdfExcelGenerator\Exceptions\ExportException
     */
    public function save(string $filename): ExportResultInterface;

    /**
     * Exporta y retorna el contenido como stream binario.
     *
     * @return string Contenido binario
     * @throws \Lopezsoft\PdfExcelGenerator\Exceptions\ExportException
     */
    public function stream(): string;

    /**
     * Valida que el exportador tiene los datos necesarios.
     *
     * @return bool
     */
    public function validate(): bool;
}

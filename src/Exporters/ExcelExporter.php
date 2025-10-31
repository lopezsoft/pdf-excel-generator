<?php

declare(strict_types=1);

namespace Lopezsoft\PdfExcelGenerator\Exporters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Lopezsoft\PdfExcelGenerator\Exceptions\ExportException;

/**
 * Exportador de Excel usando phpoffice/phpspreadsheet.
 *
 * Convierte arrays de datos a archivos Excel (XLSX, XLS, CSV).
 */
class ExcelExporter extends AbstractExporter
{
    /**
     * Tipo de writer a usar (xlsx, xls, csv).
     */
    private string $writerType = 'xlsx';

    /**
     * Título de la hoja de cálculo.
     */
    private string $sheetTitle = 'Sheet1';

    /**
     * Opciones adicionales del writer.
     *
     * @var array<string, mixed>
     */
    private array $writerOptions = [];

    /**
     * Establece el tipo de writer.
     *
     * @param string $type xlsx, xls, o csv
     * @return self
     */
    public function setWriterType(string $type): self
    {
        $this->writerType = strtolower($type);
        return $this;
    }

    /**
     * Establece el título de la hoja.
     *
     * @param string $title
     * @return self
     */
    public function setSheetTitle(string $title): self
    {
        $this->sheetTitle = $title;
        return $this;
    }

    /**
     * Establece opciones del writer.
     *
     * @param array<string, mixed> $options
     * @return self
     */
    public function setWriterOptions(array $options): self
    {
        $this->writerOptions = $options;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function validate(): bool
    {
        return !empty($this->data) && is_array($this->data);
    }

    /**
     * {@inheritDoc}
     */
    protected function getMissingDataType(): string
    {
        return 'data';
    }

    /**
     * {@inheritDoc}
     */
    protected function generate(): string
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle($this->sheetTitle);

            // Insertar datos
            $this->populateSheet($sheet);

            // Crear writer según el tipo
            $writer = $this->createWriter($spreadsheet);

            // Aplicar opciones del writer
            $this->applyWriterOptions($writer);

            // Generar contenido en memoria
            ob_start();
            $writer->save('php://output');
            $content = ob_get_clean();

            if ($content === false) {
                throw ExportException::streamFailed('Failed to generate Excel content');
            }

            return $content;
        } catch (\Throwable $e) {
            throw ExportException::streamFailed($e->getMessage());
        }
    }

    /**
     * Llena la hoja con los datos.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @return void
     */
    private function populateSheet($sheet): void
    {
        $rowIndex = 1;

        foreach ($this->data as $row) {
            $columnIndex = 1;
            foreach ($row as $value) {
                $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, $value);
                $columnIndex++;
            }
            $rowIndex++;
        }
    }

    /**
     * Crea el writer apropiado según el tipo.
     *
     * @param Spreadsheet $spreadsheet
     * @return \PhpOffice\PhpSpreadsheet\Writer\IWriter
     * @throws ExportException
     */
    private function createWriter(Spreadsheet $spreadsheet)
    {
        return match ($this->writerType) {
            'xlsx' => new Xlsx($spreadsheet),
            'xls' => new Xls($spreadsheet),
            'csv' => new Csv($spreadsheet),
            default => throw ExportException::streamFailed("Invalid writer type: {$this->writerType}"),
        };
    }

    /**
     * Aplica opciones al writer.
     *
     * @param mixed $writer
     * @return void
     */
    private function applyWriterOptions($writer): void
    {
        foreach ($this->writerOptions as $method => $value) {
            if (method_exists($writer, $method)) {
                $writer->{$method}($value);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function validateBeforeSave(): void
    {
        parent::validateBeforeSave();
        // La validación de extensión se hará en el método save con el filename real
    }
}

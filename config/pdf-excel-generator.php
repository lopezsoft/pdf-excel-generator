<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default Storage Disk
    |--------------------------------------------------------------------------
    |
    | Disco de almacenamiento predeterminado para los archivos generados.
    | Debe ser uno de los discos configurados en config/filesystems.php
    |
    */
    'disk' => env('PDF_EXCEL_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default PDF Format
    |--------------------------------------------------------------------------
    |
    | Formato predeterminado para los PDFs generados.
    | Opciones: A4, letter, legal, A3, A5, etc.
    |
    */
    'format' => env('PDF_EXCEL_FORMAT', 'A4'),

    /*
    |--------------------------------------------------------------------------
    | Chrome/Chromium Path
    |--------------------------------------------------------------------------
    |
    | Ruta personalizada al ejecutable de Chrome o Chromium.
    | Si es null, Browsershot intentará detectarlo automáticamente.
    |
    | Ejemplos:
    | - Windows: 'C:/Program Files/Google/Chrome/Application/chrome.exe'
    | - Linux: '/usr/bin/chromium-browser'
    | - macOS: '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome'
    |
    */
    'chrome_path' => env('CHROME_PATH', null),

    /*
    |--------------------------------------------------------------------------
    | PDF Options
    |--------------------------------------------------------------------------
    |
    | Opciones predeterminadas para la generación de PDFs.
    | Estas opciones se aplicarán a todos los PDFs a menos que se sobrescriban.
    |
    */
    'pdf' => [
        /*
         | Márgenes del PDF en milímetros (top, right, bottom, left)
         */
        'margins' => [
            'top' => 10,
            'right' => 10,
            'bottom' => 10,
            'left' => 10,
        ],

        /*
         | Orientación del PDF: portrait o landscape
         */
        'orientation' => 'portrait',

        /*
         | Escala de la página (0.1 a 2)
         */
        'scale' => 1,

        /*
         | Incluir fondos de página
         */
        'print_background' => true,

        /*
         | Timeout en segundos para la generación del PDF
         */
        'timeout' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Excel Options
    |--------------------------------------------------------------------------
    |
    | Opciones predeterminadas para la generación de archivos Excel.
    |
    */
    'excel' => [
        /*
         | Título predeterminado de la hoja
         */
        'sheet_title' => 'Sheet1',

        /*
         | Tipo de writer predeterminado: xlsx, xls, csv
         */
        'writer_type' => 'xlsx',

        /*
         | Autosize de columnas
         */
        'auto_size' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Blade Templates Path
    |--------------------------------------------------------------------------
    |
    | Ruta base para los templates Blade utilizados en la generación.
    | Por defecto usa las views de Laravel.
    |
    */
    'templates_path' => resource_path('views'),

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    |
    | Configuración de seguridad para validación de rutas y archivos.
    |
    */
    'security' => [
        /*
         | Validar paths para prevenir directory traversal
         */
        'validate_paths' => true,

        /*
         | Sanitizar nombres de archivo
         */
        'sanitize_filenames' => true,

        /*
         | Extensiones permitidas para PDFs
         */
        'allowed_pdf_extensions' => ['pdf'],

        /*
         | Extensiones permitidas para Excel
         */
        'allowed_excel_extensions' => ['xlsx', 'xls', 'csv'],
    ],
];

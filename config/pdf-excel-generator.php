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
    | Chrome Pool (Advanced)
    |--------------------------------------------------------------------------
    |
    | Configuración del pool de Chrome para generación de PDFs de alta concurrencia.
    | El pool reutiliza instancias de Chrome, reduciendo tiempo de generación de ~4s a ~1.5s.
    |
    | ADVERTENCIA: El pool consume ~150MB de RAM adicional. Solo usar si:
    | - Generas >10 PDFs por minuto
    | - Tu servidor tiene suficiente memoria (>2GB RAM recomendado)
    | - Necesitas optimizar rendimiento en escenarios de alta carga
    |
    */
    'chrome_pool' => [
        /*
         | Habilitar/deshabilitar Chrome Pool
         | true: Usa pool de Chrome (mayor rendimiento, más memoria)
         | false: Lanza Chrome por cada PDF (menor rendimiento, menos memoria)
         */
        'enabled' => env('CHROME_POOL_ENABLED', false),

        /*
         | Puerto de debugging remoto de Chrome
         | Puerto donde Chrome escuchará conexiones WebSocket
         | Si es null, se selecciona un puerto aleatorio disponible (9222-9999)
         */
        'debug_port' => env('CHROME_POOL_DEBUG_PORT', null),

        /*
         | Tiempo de espera en segundos para iniciar Chrome
         | Incrementar si Chrome tarda en iniciar en servidores lentos
         */
        'startup_timeout' => env('CHROME_POOL_STARTUP_TIMEOUT', 5),

        /*
         | Reintentos para conectar al pool si falla
         | Útil para recuperación automática si Chrome crashea
         */
        'connection_retries' => env('CHROME_POOL_CONNECTION_RETRIES', 3),

        /*
         | Reiniciar Chrome automáticamente si no responde
         | Detecta crashes y reinicia el proceso automáticamente
         */
        'auto_restart' => env('CHROME_POOL_AUTO_RESTART', true),
    ],

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

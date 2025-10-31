# PDF Excel Generator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lopezsoft/pdf-excel-generator.svg?style=flat-square)](https://packagist.org/packages/lopezsoft/pdf-excel-generator)
[![Total Downloads](https://img.shields.io/packagist/dt/lopezsoft/pdf-excel-generator.svg?style=flat-square)](https://packagist.org/packages/lopezsoft/pdf-excel-generator)
[![License](https://img.shields.io/packagist/l/lopezsoft/pdf-excel-generator.svg?style=flat-square)](https://packagist.org/packages/lopezsoft/pdf-excel-generator)

Una librería Laravel moderna y robusta para generar PDFs y archivos Excel desde HTML, plantillas Blade o datos estructurados.

## 🚀 Características

- ✅ **Generación de PDFs** desde HTML o Blade usando Puppeteer/Chrome (vía spatie/browsershot)
- ✅ **Generación de Excel** (XLSX, XLS, CSV) desde arrays de datos (vía PhpSpreadsheet)
- ✅ **API Fluida** intuitiva y fácil de usar
- ✅ **Validación robusta** de rutas y templates
- ✅ **Seguridad** contra directory traversal y path injection
- ✅ **Múltiples discos** de almacenamiento
- ✅ **URLs descargables** automáticas
- ✅ **Stream y Download** directo sin guardar en disco
- ✅ **Laravel 9.x, 10.x, 11.x** compatible
- ✅ **PHP 8.1+** con strict types
- ✅ **SOLID principles** y Clean Code
- ✅ **Tests completos** incluidos

## 📋 Requisitos

- PHP >= 8.1
- Laravel >= 9.0
- Chrome/Chromium instalado (para PDFs)
- Composer

## 📦 Instalación

```bash
composer require lopezsoft/pdf-excel-generator
```

### Publicar configuración (opcional)

```bash
php artisan vendor:publish --tag=pdf-excel-generator-config
```

### Instalar Puppeteer (para PDFs)

La generación de PDFs requiere Puppeteer/Chrome. Instálalo con npm:

```bash
npm install puppeteer
```

**Alternativa:** Si prefieres usar tu Chrome/Chromium local:

#### Ubuntu/Debian
```bash
sudo apt-get install chromium-browser
```

#### macOS
```bash
brew install --cask google-chrome
```

#### Windows
Descarga e instala Chrome desde [google.com/chrome](https://www.google.com/chrome/)

Luego configura la ruta en `.env`:
```env
CHROME_PATH="C:/Program Files/Google/Chrome/Application/chrome.exe"
```

## 🎯 Uso Básico

### Generar PDF desde HTML

```php
use Lopezsoft\PdfExcelGenerator\Facades\PdfExcelGenerator;

// PDF desde HTML
$pdf = PdfExcelGenerator::html('<h1>¡Hola Mundo!</h1>')
    ->savePdf('hello.pdf');

echo $pdf->url();   // URL descargable
echo $pdf->path();  // Ruta absoluta
```

### Generar PDF desde Blade

```php
// PDF desde template Blade
$pdf = PdfExcelGenerator::blade('invoices.show', [
    'invoice' => $invoice,
    'customer' => $customer
])->savePdf('invoice-001.pdf');

return $pdf->download(); // Descarga directa
```

### Configurar Márgenes del PDF

```php
// Márgenes iguales para todos los lados
$pdf = PdfExcelGenerator::html($html)
    ->margins(20) // 20mm todos los lados
    ->savePdf('output.pdf');

// Márgenes personalizados (top, right, bottom, left)
$pdf = PdfExcelGenerator::html($html)
    ->customMargins(25, 15, 25, 15) // arriba, derecha, abajo, izquierda
    ->savePdf('output.pdf');
```

### Generar Excel desde Datos

```php
// Excel desde array
$data = [
    ['Nombre', 'Email', 'Edad'],
    ['Juan Pérez', 'juan@example.com', 30],
    ['María García', 'maria@example.com', 25],
    ['Carlos López', 'carlos@example.com', 35],
];

$excel = PdfExcelGenerator::data($data)
    ->sheetTitle('Usuarios')
    ->saveExcel('usuarios.xlsx');

echo $excel->url(); // URL descargable
```

## 🔧 Uso Avanzado

### Configurar formato y disco

```php
$pdf = PdfExcelGenerator::html($html)
    ->format('letter')      // A4, letter, legal, A3, etc.
    ->disk('s3')            // Guardar en S3
    ->savePdf('report.pdf');
```

### Stream sin guardar

```php
// Obtener contenido binario sin guardar
$pdfContent = PdfExcelGenerator::html($html)->streamPdf();
$excelContent = PdfExcelGenerator::data($data)->streamExcel();

// Retornar como respuesta HTTP
return response($pdfContent, 200, [
    'Content-Type' => 'application/pdf',
]);
```

### Opciones avanzadas de PDF

```php
$pdf = PdfExcelGenerator::html($html)
    ->format('A4')
    ->pdfOptions([
        'landscape' => true,
        'scale' => 0.8,
        'margins' => [15, 15, 15, 15],
    ])
    ->savePdf('landscape.pdf');
```

### Generar múltiples formatos

```php
// Generar CSV en lugar de XLSX
$csv = PdfExcelGenerator::data($data)
    ->saveExcel('data.csv');

// Generar XLS (formato antiguo)
$xls = PdfExcelGenerator::data($data)
    ->saveExcel('data.xls');
```

## 🛠️ Configuración

El archivo de configuración `config/pdf-excel-generator.php` incluye:

```php
return [
    // Disco de almacenamiento por defecto
    'disk' => env('PDF_EXCEL_DISK', 'local'),

    // Formato PDF por defecto
    'format' => env('PDF_EXCEL_FORMAT', 'A4'),

    // Path a Chrome/Chromium (opcional)
    'chrome_path' => env('CHROME_PATH', null),

    // Opciones de PDF
    'pdf' => [
        'margins' => [10, 10, 10, 10],
        'orientation' => 'portrait',
        'print_background' => true,
        'timeout' => 60,
    ],

    // Opciones de Excel
    'excel' => [
        'sheet_title' => 'Sheet1',
        'writer_type' => 'xlsx',
        'auto_size' => true,
    ],

    // Seguridad
    'security' => [
        'validate_paths' => true,
        'sanitize_filenames' => true,
    ],
];
```

## 📝 Ejemplos Completos

### Factura PDF con Blade

```php
// resources/views/invoices/pdf.blade.php
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { background: #007bff; color: white; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #ddd; padding: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Factura #{{ $invoice->number }}</h1>
    </div>
    <table>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio</th>
        </tr>
        @foreach($invoice->items as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>{{ $item->quantity }}</td>
            <td>${{ $item->price }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>
```

```php
// Controlador
public function downloadInvoice(Invoice $invoice)
{
    return PdfExcelGenerator::blade('invoices.pdf', compact('invoice'))
        ->format('A4')
        ->savePdf("invoice-{$invoice->number}.pdf")
        ->download();
}
```

### Reporte Excel con Datos

```php
public function exportUsers()
{
    $users = User::all();
    
    $data = [
        ['ID', 'Nombre', 'Email', 'Fecha Registro'],
    ];
    
    foreach ($users as $user) {
        $data[] = [
            $user->id,
            $user->name,
            $user->email,
            $user->created_at->format('Y-m-d'),
        ];
    }
    
    return PdfExcelGenerator::data($data)
        ->sheetTitle('Usuarios')
        ->saveExcel('usuarios-' . now()->format('Y-m-d') . '.xlsx')
        ->download();
}
```

## 🔒 Seguridad

La librería incluye validaciones de seguridad integradas:

- ✅ **Path Traversal Prevention**: Evita acceso a archivos fuera del scope
- ✅ **Filename Sanitization**: Limpia caracteres peligrosos en nombres de archivo
- ✅ **Extension Validation**: Solo permite extensiones seguras
- ✅ **Template Validation**: Verifica existencia de templates Blade

## 🧪 Testing

```bash
# Ejecutar tests
composer test

# Con coverage
composer test -- --coverage
```

### Prueba Manual

Si quieres probar la librería antes de integrarla:

```bash
# Instalar dependencias
composer install
npm install puppeteer

# Crear archivo de prueba
php -r "
require 'vendor/autoload.php';
use Lopezsoft\PdfExcelGenerator\Exporters\PdfExporter;

\$pdf = new PdfExporter();
\$pdf->setHtml('<h1>Test PDF</h1>');
file_put_contents('test.pdf', \$pdf->stream());
echo 'PDF generado: test.pdf';
"
```

## 🐛 Manejo de Errores

```php
use Lopezsoft\PdfExcelGenerator\Exceptions\ChromeNotFoundException;
use Lopezsoft\PdfExcelGenerator\Exceptions\InvalidTemplateException;
use Lopezsoft\PdfExcelGenerator\Exceptions\InvalidPdfException;
use Lopezsoft\PdfExcelGenerator\Exceptions\ExportException;

try {
    $pdf = PdfExcelGenerator::blade('invoice', $data)->savePdf('invoice.pdf');
} catch (ChromeNotFoundException $e) {
    // Chrome no está instalado
    return back()->with('error', 'Chrome no disponible para generar PDF');
} catch (InvalidTemplateException $e) {
    // Template Blade no existe
    Log::error('Template no encontrado: ' . $e->getMessage());
} catch (InvalidPdfException $e) {
    // PDF generado es corrupto o inválido
    Log::error('PDF corrupto: ' . $e->getMessage());
    return response()->json(['error' => 'Error al generar PDF'], 500);
} catch (ExportException $e) {
    // Error genérico de exportación
    return response()->json(['error' => $e->getMessage()], 500);
}
```

## ⚡ Optimización: Chrome Pool (Avanzado)

Para proyectos que generan **muchos PDFs simultáneamente** (>10/min), puedes usar el **Chrome Pool** para reutilizar instancias de Chrome y reducir el tiempo de generación de **~4s a ~1.5s**.

### Configuración

```php
use Lopezsoft\PdfExcelGenerator\Services\ChromePool;

// En AppServiceProvider::boot() o al inicio de tu aplicación
ChromePool::getInstance()->start();

// Usar normalmente (automáticamente detecta el pool)
$pdf = PdfExcelGenerator::html($html)->savePdf('output.pdf');

// Al finalizar la aplicación (opcional, Laravel lo hace automáticamente)
ChromePool::getInstance()->stop();
```

### Cuándo Usar Chrome Pool

✅ **SÍ usar si:**
- Generas >10 PDFs por minuto
- Tu aplicación tiene alta concurrencia
- Tienes un worker dedicado para PDFs

❌ **NO usar si:**
- Generas PDFs esporádicamente (<5/min)
- Tu servidor tiene memoria limitada (<2GB RAM)
- Solo generas PDFs bajo demanda del usuario

**Advertencia:** El pool mantiene Chrome en memoria (~150MB). Solo usar si el beneficio de rendimiento justifica el consumo de recursos.
```

## 📚 API Reference

### PdfExcelGenerator

| Método | Descripción |
|--------|-------------|
| `html(string $html)` | Establece contenido HTML |
| `blade(string $template, array $data)` | Usa template Blade |
| `data(array $data)` | Establece datos para Excel |
| `format(string $format)` | Formato del documento |
| `disk(string $disk)` | Disco de almacenamiento |
| `savePdf(string $filename)` | Guarda PDF y retorna ExportResult |
| `saveExcel(string $filename)` | Guarda Excel y retorna ExportResult |
| `streamPdf()` | Retorna contenido binario PDF |
| `streamExcel()` | Retorna contenido binario Excel |
| `pdfOptions(array $options)` | Opciones adicionales PDF |
| `sheetTitle(string $title)` | Título de hoja Excel |

### ExportResult

| Método | Descripción |
|--------|-------------|
| `url()` | URL descargable del archivo |
| `path()` | Ruta absoluta del archivo |
| `stream()` | Contenido binario |
| `download(?string $name)` | Respuesta HTTP para descarga |
| `filename()` | Nombre del archivo |
| `disk()` | Disco donde se guardó |

## 🤝 Contribuir

Las contribuciones son bienvenidas. Por favor:

1. Fork el repositorio
2. Crea una rama para tu feature (`git checkout -b feature/amazing-feature`)
3. Commit tus cambios (`git commit -m 'feat: add amazing feature'`)
4. Push a la rama (`git push origin feature/amazing-feature`)
5. Abre un Pull Request

## 📄 Licencia

MIT License. Ver [LICENSE](LICENSE) para más información.

## 👨‍💻 Autor

**Lewis Lopez** - [lopezsoft](https://github.com/lopezsoft)

- Email: lopezsoft.com@gmail.com

## 🙏 Créditos

Esta librería utiliza:

- [spatie/browsershot](https://github.com/spatie/browsershot) - Generación de PDFs
- [phpoffice/phpspreadsheet](https://github.com/PHPOffice/PhpSpreadsheet) - Generación de Excel

## 📮 Soporte

Si encuentras un bug o tienes una sugerencia:

- [Abrir un Issue](https://github.com/lopezsoft/pdf-excel-generator/issues)
- [Discusiones](https://github.com/lopezsoft/pdf-excel-generator/discussions)

---

Desarrollado con ❤️ por [lopezsoft](https://github.com/lopezsoft)

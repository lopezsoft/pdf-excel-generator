# Ejemplo de Uso - Subdirectorios (v1.2.1)

## Problema Solucionado

En v1.1.1, este código **NO funcionaba**:
```php
PdfExcelGenerator::blade('invoice', $data)
    ->disk('pdf')
    ->savePdf('1/e3efe7ab-b028-11f0-be83-d843ae899220.pdf');
```
- ❌ Guardaba en: `storage/app/pdf/e3efe7ab-b028-11f0-be83-d843ae899220.pdf` (raíz)
- ❌ **NO** creaba el directorio `1/`

En v1.2.1, **AHORA SÍ funciona**:
```php
PdfExcelGenerator::blade('invoice', $data)
    ->disk('pdf')
    ->savePdf('1/e3efe7ab-b028-11f0-be83-d843ae899220.pdf');
```
- ✅ Guarda en: `storage/app/pdf/1/e3efe7ab-b028-11f0-be83-d843ae899220.pdf`
- ✅ Crea automáticamente el directorio `1/`

## Uso Correcto

### 1. Instalar/Actualizar a v1.2.1

```bash
composer require lopezsoft/pdf-excel-generator:^1.2.1
```

O en `composer.json`:
```json
{
    "require": {
        "lopezsoft/pdf-excel-generator": "^1.2.1"
    }
}
```

### 2. Configurar Disco en `config/filesystems.php`

```php
'disks' => [
    'pdf' => [
        'driver' => 'local',
        'root' => storage_path('app/pdf'),
        'url' => env('APP_URL').'/storage/pdf',
        'visibility' => 'public',
    ],
],
```

### 3. Guardar PDFs con Subdirectorios

```php
use Lopezsoft\PdfExcelGenerator\Facades\PdfExcelGenerator;

// Caso 1: Subdirectorio numérico
$result = PdfExcelGenerator::blade('invoice', $data)
    ->disk('pdf')
    ->savePdf('1/e3efe7ab-b028-11f0-be83-d843ae899220.pdf');

// Verificar
echo $result->filename(); // "1/e3efe7ab-b028-11f0-be83-d843ae899220.pdf"
echo $result->path();     // "/path/to/storage/app/pdf/1/e3efe7ab-b028-11f0-be83-d843ae899220.pdf"
```

```php
// Caso 2: Múltiples niveles
$result = PdfExcelGenerator::blade('report', $data)
    ->disk('pdf')
    ->savePdf('invoices/2025/10/invoice-001.pdf');

// Se crea automáticamente: invoices/ → 2025/ → 10/
```

```php
// Caso 3: Con fecha dinámica
$userId = 1;
$uuid = Str::uuid();
$path = "{$userId}/{$uuid}.pdf";

$result = PdfExcelGenerator::blade('document', $data)
    ->disk('pdf')
    ->savePdf($path);

// Guarda en: storage/app/pdf/1/{uuid}.pdf
```

### 4. Verificar que Funciona

```php
use Illuminate\Support\Facades\Storage;

$result = PdfExcelGenerator::blade('test', $data)
    ->disk('pdf')
    ->savePdf('1/test.pdf');

// Verificar con Storage
if (Storage::disk('pdf')->exists('1/test.pdf')) {
    echo "✓ Archivo guardado correctamente en subdirectorio '1/'";
    
    // Listar archivos en el subdirectorio
    $files = Storage::disk('pdf')->files('1');
    foreach ($files as $file) {
        echo "- $file\n";
    }
} else {
    echo "✗ ERROR: Archivo no encontrado";
}

// Verificar con $result
if ($result->exists()) {
    echo "✓ Archivo existe según ExportResult";
}
```

## Casos de Uso Comunes

### Organizar por Usuario
```php
public function generateInvoice(User $user)
{
    $filename = "{$user->id}/" . Str::uuid() . '.pdf';
    
    return PdfExcelGenerator::blade('invoice', ['user' => $user])
        ->disk('pdf')
        ->savePdf($filename);
}
```

### Organizar por Fecha
```php
public function generateMonthlyReport()
{
    $date = now();
    $filename = "reports/{$date->year}/{$date->month}/monthly.pdf";
    
    return PdfExcelGenerator::blade('report', $data)
        ->disk('pdf')
        ->savePdf($filename);
}
```

### Organizar por Tipo de Documento
```php
public function saveDocument(string $type, array $data)
{
    $types = [
        'invoice' => 'invoices',
        'receipt' => 'receipts',
        'contract' => 'contracts',
    ];
    
    $directory = $types[$type] ?? 'documents';
    $filename = "{$directory}/" . Str::uuid() . '.pdf';
    
    return PdfExcelGenerator::blade($type, $data)
        ->disk('pdf')
        ->savePdf($filename);
}
```

## Changelog del Fix

### v1.2.1 (2025-10-30)
- **FIXED**: `PathValidator::sanitizeFilename()` preserva estructura de directorios
- **FIXED**: Directorios anidados se crean automáticamente
- **ADDED**: Tests para subdirectorios numéricos
- **ADDED**: Soporte para backslashes Windows (`docs\reports\file.pdf`)

### Diferencia Técnica

**ANTES (v1.1.1)**:
```php
// PathValidator::sanitizeFilename()
$pathinfo = pathinfo($filename);  // "1/file.pdf" → ['filename' => 'file']
return "{$name}.{$extension}";    // Retorna solo "file.pdf" ❌
```

**DESPUÉS (v1.2.1)**:
```php
// PathValidator::sanitizeFilename()
$directory = dirname($filename);   // "1/file.pdf" → "1"
$basename = basename($filename);   // "1/file.pdf" → "file.pdf"
// Sanitiza solo el basename, preserva el directorio
return "{$directory}/{$sanitizedBasename}";  // Retorna "1/file.pdf" ✅
```

## Soporte

Si después de actualizar a v1.2.1 aún tienes problemas:

1. Verificar versión instalada:
```bash
composer show lopezsoft/pdf-excel-generator
```

2. Limpiar cache de Composer:
```bash
composer clear-cache
composer update lopezsoft/pdf-excel-generator
```

3. Verificar configuración del disco en `config/filesystems.php`

4. Verificar permisos de escritura en `storage/app/pdf`

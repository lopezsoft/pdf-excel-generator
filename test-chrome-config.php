<?php

/**
 * Script de diagnóstico para verificar la configuración de Chrome.
 * 
 * Ejecutar desde la raíz del proyecto:
 * php test-chrome-config.php
 */

require __DIR__ . '/vendor/autoload.php';

echo "=================================================\n";
echo "  DIAGNÓSTICO DE CONFIGURACIÓN DE CHROME\n";
echo "=================================================\n\n";

// Simular carga de .env
if (file_exists(__DIR__ . '/.env')) {
    $envContent = file_get_contents(__DIR__ . '/.env');
    preg_match('/CHROME_PATH=(.+)/', $envContent, $matches);
    $envChromePath = $matches[1] ?? null;
    
    if ($envChromePath) {
        // Remover comillas si existen
        $envChromePath = trim($envChromePath, '\'"');
        putenv("CHROME_PATH={$envChromePath}");
    }
}

echo "1. VERIFICACIÓN DE VARIABLES DE ENTORNO\n";
echo "----------------------------------------\n";
$chromePath = getenv('CHROME_PATH');
echo "CHROME_PATH: " . ($chromePath ?: "❌ NO CONFIGURADO") . "\n";

if ($chromePath) {
    echo "  ✓ Variable de entorno encontrada\n";
} else {
    echo "  ❌ Variable de entorno no encontrada\n";
    echo "  → Configura CHROME_PATH en tu .env\n";
}
echo "\n";

echo "2. DETECCIÓN AUTOMÁTICA DE CHROME\n";
echo "----------------------------------------\n";

$possiblePaths = [
    '/usr/bin/google-chrome',
    '/usr/bin/google-chrome-stable',
    '/usr/bin/chromium-browser',
    '/usr/bin/chromium',
    '/snap/bin/chromium',
    'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
    'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
];

$foundPaths = [];
foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        echo "✓ Encontrado: {$path}\n";
        $foundPaths[] = $path;
    }
}

if (empty($foundPaths)) {
    echo "❌ No se encontró ningún ejecutable de Chrome/Chromium\n";
    echo "\nINSTALACIÓN REQUERIDA:\n";
    if (PHP_OS_FAMILY === 'Linux') {
        echo "  Ubuntu/Debian:\n";
        echo "    sudo apt-get update\n";
        echo "    sudo apt-get install google-chrome-stable\n";
        echo "  O:\n";
        echo "    sudo apt-get install chromium-browser\n";
    } elseif (PHP_OS_FAMILY === 'Windows') {
        echo "  Descarga Chrome desde:\n";
        echo "    https://www.google.com/chrome/\n";
    }
} else {
    echo "\nPaths sugeridos para .env:\n";
    foreach ($foundPaths as $path) {
        echo "  CHROME_PATH={$path}\n";
    }
}
echo "\n";

echo "3. VERIFICACIÓN DEL PATH CONFIGURADO\n";
echo "----------------------------------------\n";
$configuredPath = $chromePath ?: ($foundPaths[0] ?? null);

if ($configuredPath) {
    echo "Path a verificar: {$configuredPath}\n";
    
    // Verificar existencia
    if (file_exists($configuredPath)) {
        echo "✓ El archivo existe\n";
        
        // Verificar permisos
        if (is_executable($configuredPath)) {
            echo "✓ El archivo es ejecutable\n";
        } else {
            echo "❌ El archivo NO es ejecutable\n";
            echo "  → Ejecuta: chmod +x {$configuredPath}\n";
        }
        
        // Intentar obtener versión
        $versionOutput = [];
        $returnCode = 0;
        @exec("{$configuredPath} --version 2>&1", $versionOutput, $returnCode);
        
        if ($returnCode === 0 && !empty($versionOutput)) {
            echo "✓ Chrome ejecutable correctamente\n";
            echo "  Versión: " . implode("\n           ", $versionOutput) . "\n";
        } else {
            echo "⚠ No se pudo ejecutar Chrome\n";
            echo "  Output: " . implode("\n          ", $versionOutput) . "\n";
            echo "  Return code: {$returnCode}\n";
        }
        
        // Verificar dependencias en Linux
        if (PHP_OS_FAMILY === 'Linux') {
            echo "\n4. VERIFICACIÓN DE DEPENDENCIAS (Linux)\n";
            echo "----------------------------------------\n";
            
            exec("ldd {$configuredPath} 2>&1 | grep 'not found'", $missingDeps);
            
            if (empty($missingDeps)) {
                echo "✓ Todas las dependencias están instaladas\n";
            } else {
                echo "❌ Dependencias faltantes:\n";
                foreach ($missingDeps as $dep) {
                    echo "  - {$dep}\n";
                }
                echo "\n  → Ejecuta: sudo apt-get install -f\n";
            }
        }
        
    } else {
        echo "❌ El archivo NO existe en la ruta especificada\n";
        echo "  → Verifica la ruta en tu .env\n";
        echo "  → Paths disponibles arriba\n";
    }
} else {
    echo "❌ No hay ningún path configurado ni detectado\n";
}
echo "\n";

echo "5. INFORMACIÓN DEL SISTEMA\n";
echo "----------------------------------------\n";
echo "Sistema Operativo: " . PHP_OS . "\n";
echo "Familia OS: " . PHP_OS_FAMILY . "\n";
echo "Versión PHP: " . PHP_VERSION . "\n";
echo "Usuario actual: " . get_current_user() . "\n";

if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
    $userInfo = posix_getpwuid(posix_geteuid());
    echo "Usuario efectivo: " . $userInfo['name'] . "\n";
}

echo "\n";

echo "6. PRUEBA DE BROWSERSHOT (si está instalado)\n";
echo "----------------------------------------\n";

if (class_exists('Spatie\\Browsershot\\Browsershot')) {
    echo "✓ Browsershot está instalado\n";
    
    try {
        $browsershot = \Spatie\Browsershot\Browsershot::html('<h1>Test</h1>');
        
        if ($configuredPath) {
            $browsershot->setChromePath($configuredPath);
        }
        
        $tempFile = tempnam(sys_get_temp_dir(), 'chrome_test_') . '.pdf';
        
        echo "  Generando PDF de prueba...\n";
        $browsershot->save($tempFile);
        
        if (file_exists($tempFile) && filesize($tempFile) > 0) {
            echo "✓ PDF generado exitosamente\n";
            echo "  Tamaño: " . filesize($tempFile) . " bytes\n";
            echo "  Path: {$tempFile}\n";
            unlink($tempFile);
        } else {
            echo "❌ Error al generar PDF\n";
        }
        
    } catch (\Exception $e) {
        echo "❌ Error al ejecutar Browsershot:\n";
        echo "  " . $e->getMessage() . "\n";
    }
} else {
    echo "⚠ Browsershot no está instalado\n";
    echo "  → composer require spatie/browsershot\n";
}

echo "\n";

echo "=================================================\n";
echo "  RESUMEN Y RECOMENDACIONES\n";
echo "=================================================\n\n";

$issues = [];
$recommendations = [];

if (!$chromePath) {
    $issues[] = "Variable CHROME_PATH no configurada en .env";
    $recommendations[] = "Añade CHROME_PATH a tu .env con uno de los paths encontrados arriba";
}

if ($configuredPath && !file_exists($configuredPath)) {
    $issues[] = "El path configurado no existe";
    $recommendations[] = "Verifica la ruta en .env o usa uno de los paths detectados";
}

if ($configuredPath && file_exists($configuredPath) && !is_executable($configuredPath)) {
    $issues[] = "Chrome no es ejecutable";
    $recommendations[] = "Ejecuta: chmod +x {$configuredPath}";
}

if (empty($foundPaths)) {
    $issues[] = "Chrome/Chromium no está instalado";
    $recommendations[] = "Instala Chrome o Chromium según las instrucciones arriba";
}

if (!empty($issues)) {
    echo "❌ PROBLEMAS ENCONTRADOS:\n";
    foreach ($issues as $i => $issue) {
        echo "  " . ($i + 1) . ". {$issue}\n";
    }
    echo "\n";
    
    echo "💡 RECOMENDACIONES:\n";
    foreach ($recommendations as $i => $rec) {
        echo "  " . ($i + 1) . ". {$rec}\n";
    }
    echo "\n";
} else {
    echo "✓ ¡Todo parece estar configurado correctamente!\n\n";
}

echo "Para más información, consulta TROUBLESHOOTING.md\n";
echo "=================================================\n";

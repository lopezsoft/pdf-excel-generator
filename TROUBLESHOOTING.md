# Troubleshooting Guide

## Chrome/Chromium Not Found Error

### Error Message
```
Chrome/Chromium not found. Please install Chrome or Chromium and ensure it's in your PATH,
or configure the chrome_path in config/pdf-excel-generator.php
```

### Causa
El paquete no puede localizar el ejecutable de Chrome/Chromium necesario para generar PDFs.

### Soluciones

#### 1. Verificar que Chrome/Chromium esté instalado

**Ubuntu/Debian:**
```bash
# Verificar si está instalado
which google-chrome
which chromium-browser

# Si no está instalado, instalar Google Chrome:
wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
sudo dpkg -i google-chrome-stable_current_amd64.deb
sudo apt-get install -f

# O instalar Chromium:
sudo apt-get update
sudo apt-get install chromium-browser
```

**CentOS/RHEL:**
```bash
# Google Chrome
sudo yum install google-chrome-stable

# Chromium
sudo yum install chromium
```

#### 2. Configurar la ruta en .env

Una vez instalado Chrome/Chromium, configura la ruta en tu archivo `.env`:

```env
# Para Google Chrome
CHROME_PATH=/usr/bin/google-chrome

# Para Chromium
CHROME_PATH=/usr/bin/chromium-browser
```

#### 3. Verificar permisos de ejecución

Asegúrate de que el ejecutable tenga permisos de ejecución:

```bash
# Verificar permisos
ls -la /usr/bin/google-chrome

# Dar permisos de ejecución si es necesario
sudo chmod +x /usr/bin/google-chrome
```

#### 4. Verificar la configuración

Verifica que el archivo de configuración esté publicado y correctamente configurado:

```bash
# Publicar configuración si no existe
php artisan vendor:publish --tag=pdf-excel-config

# Verificar contenido
cat config/pdf-excel-generator.php | grep chrome_path
```

#### 5. Probar manualmente

Ejecuta Chrome desde la terminal para verificar que funciona:

```bash
# Debería mostrar la versión
/usr/bin/google-chrome --version

# O para Chromium
/usr/bin/chromium-browser --version
```

### Configuración en Servidores (Ubuntu 22.04)

Para servidores sin interfaz gráfica, Chrome necesita dependencias adicionales:

```bash
# Instalar dependencias necesarias
sudo apt-get update
sudo apt-get install -y \
    libnss3 \
    libatk1.0-0 \
    libatk-bridge2.0-0 \
    libcups2 \
    libdrm2 \
    libxkbcommon0 \
    libxcomposite1 \
    libxdamage1 \
    libxrandr2 \
    libgbm1 \
    libasound2

# Instalar Google Chrome
wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
sudo dpkg -i google-chrome-stable_current_amd64.deb
sudo apt-get install -f -y

# Verificar instalación
google-chrome --version
```

### Configuración Completa del .env

Ejemplo de configuración completa en `.env`:

```env
# Ruta del ejecutable de Chrome (OBLIGATORIO)
CHROME_PATH=/usr/bin/google-chrome

# Pool de Puppeteer para alta concurrencia (OPCIONAL)
# true = Mantiene Chrome activo (requiere ≥4GB RAM)
# false = Lanza Chrome nuevo por cada PDF (más lento pero menos RAM)
CHROME_POOL_ENABLED=false

# Configuración de pdf-excel-generator
PDF_EXCEL_DISK=pdf
PDF_EXCEL_FORMAT=A4
```

### Verificar desde PHP

Puedes verificar que la configuración sea correcta ejecutando:

```php
// En tinker o en un controlador de prueba
php artisan tinker

>>> config('pdf-excel-generator.chrome_path')
=> "/usr/bin/google-chrome"

>>> file_exists(config('pdf-excel-generator.chrome_path'))
=> true
```

### Debugging Adicional

Si el problema persiste, verifica los logs:

```bash
# Ver logs de Laravel
tail -f storage/logs/laravel.log

# Verificar que el usuario web tenga acceso
sudo -u www-data google-chrome --version
```

---

## Problemas Comunes de Permisos

### Error: "Cannot run Chrome as root"

Chrome no puede ejecutarse como root por defecto. Si usas Docker o ejecutas como root:

```bash
# Opción 1: Ejecutar con usuario no-root
RUN useradd -m -s /bin/bash appuser
USER appuser

# Opción 2: Forzar ejecución como root (NO RECOMENDADO para producción)
# En tu código, añadir al .env:
CHROME_NO_SANDBOX=true
```

### Error: "Failed to launch Chrome"

Verifica que todas las dependencias estén instaladas:

```bash
# Ubuntu/Debian
ldd /usr/bin/google-chrome | grep "not found"

# Si hay librerías faltantes, instalarlas
sudo apt-get install -f
```

---

## Errores de Memoria

### Error: "Out of memory" o Chrome crashea

Incrementa la memoria disponible o deshabilita el pool:

```env
# .env
CHROME_POOL_ENABLED=false

# O incrementa memoria PHP
PHP_MEMORY_LIMIT=512M
```

---

## Contacto

Si ninguna solución funciona, abre un issue en GitHub con:
1. Sistema operativo y versión
2. Versión de Chrome (`google-chrome --version`)
3. Salida de `ls -la $(which google-chrome)`
4. Contenido de `config('pdf-excel-generator.chrome_path')`
5. Logs completos del error

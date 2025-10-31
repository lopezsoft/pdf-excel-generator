# ✅ Solución DEFINITIVA: Chrome con open_basedir en Plesk/cPanel

## El Problema

Servidores con Plesk o cPanel configuran la directiva PHP `open_basedir` que restringe el acceso a ciertos directorios. Aunque puedes crear un symlink `/tmp/google-chrome` → `/opt/google/chrome/google-chrome`, cuando Puppeteer intenta ejecutar Chrome, el sistema operativo **sigue el symlink** al directorio real (`/opt/google/chrome/`) que está **bloqueado por `open_basedir`**.

**Error típico:**
```
Error: Browser was not found at the configured executablePath (/tmp/google-chrome)
```

---

## ✅ Solución que FUNCIONA (Probada)

**Instala Chrome localmente en tu proyecto usando Puppeteer:**

### Paso 1: Instala Puppeteer

```bash
cd /var/www/vhosts/tu-dominio.com/httpdocs
npm install puppeteer
```

### Paso 2: Descarga Chrome

```bash
npx @puppeteer/browsers install chrome@stable
```

Esto descarga Chrome (~170MB) en `chrome/linux-XXXXXX/chrome-linux64/chrome` dentro de tu proyecto.

### Paso 3: Encuentra el path exacto

```bash
ls -la chrome/linux-*/chrome-linux64/chrome
```

**Ejemplo de salida:**
```
-rwxr-xr-x 1 user group 123456789 Oct 31 03:00 chrome/linux-142.0.7444.59/chrome-linux64/chrome
```

### Paso 4: Configura en .env

```bash
# Reemplaza con tu path REAL
CHROME_PATH=/var/www/vhosts/tu-dominio.com/httpdocs/chrome/linux-142.0.7444.59/chrome-linux64/chrome
```

⚠️ **IMPORTANTE:** Usa el **path absoluto completo**, reemplazando:
- `tu-dominio.com` con tu dominio real
- `httpdocs` con tu directorio raíz (puede ser `apiv2`, `public_html`, etc.)
- `linux-142.0.7444.59` con la versión que se descargó

### Paso 5: Verifica que funcione

```bash
# Debe mostrar: Google Chrome 142.0.7444.59
/var/www/vhosts/tu-dominio.com/httpdocs/chrome/linux-142.0.7444.59/chrome-linux64/chrome --version
```

### Paso 6: Prueba generación de PDF

```bash
php artisan tinker
```

```php
use Lopezsoft\PdfExcelGenerator\Facades\PdfExcelGenerator;

$pdf = PdfExcelGenerator::html('<h1>¡Funciona!</h1>')
    ->savePdf('test.pdf');

echo $pdf->path();  // Debe crear el PDF sin errores
```

---

## ❌ Lo que NO funciona

### ❌ Symlinks
```bash
# NO FUNCIONA con open_basedir
ln -s /opt/google/chrome/google-chrome /tmp/google-chrome
```

Aunque el symlink exista, cuando Puppeteer ejecuta `/tmp/google-chrome`, el kernel sigue el enlace a `/opt/google/chrome/` que está fuera de `open_basedir`.

### ❌ Modificar open_basedir sin permisos
```bash
# NO FUNCIONA si no eres admin
echo "open_basedir = /var/www/:/tmp/:/opt/google/chrome/" >> php.ini
```

Solo el administrador del servidor puede modificar esta directiva.

---

## 🔧 Soluciones Alternativas (Si tienes acceso admin)

### Opción A: Modificar open_basedir en Plesk

1. Ve a `Dominios` → Tu dominio → `Configuración de PHP`
2. Busca `open_basedir`
3. Agrega: `:/opt/google/chrome/`
4. Ejemplo final: `/var/www/vhosts/tu-dominio.com/:/tmp/:/opt/google/chrome/`
5. Guarda y recarga PHP-FPM

### Opción B: Docker (Producción profesional)

```dockerfile
FROM php:8.1-fpm
RUN apt-get update && apt-get install -y google-chrome-stable nodejs npm
# Chrome estará disponible sin restricciones de open_basedir
```

---

## 📊 Comparación de Soluciones

| Solución | Ventajas | Desventajas | Recomendado |
|----------|----------|-------------|-------------|
| **Puppeteer local** | ✅ Sin permisos admin<br>✅ Funciona con open_basedir<br>✅ Versionado por proyecto | ❌ ~170MB por proyecto<br>❌ Duplica Chrome | ✅ **SÍ** |
| **Modificar open_basedir** | ✅ Chrome compartido<br>✅ Menos espacio | ❌ Requiere admin<br>❌ Riesgo de seguridad | ⚠️ Solo si tienes acceso |
| **Symlinks** | ✅ Fácil de crear | ❌ **NO FUNCIONA** con open_basedir | ❌ **NO** |
| **Docker** | ✅ Sin restricciones<br>✅ Reproducible | ❌ Requiere infraestructura Docker | ⚠️ Para producción avanzada |

---

## 🎯 Conclusión

**Para la mayoría de servidores con Plesk/cPanel:**

```bash
# 1. Instala Chrome local
cd tu-proyecto && npm install puppeteer
npx @puppeteer/browsers install chrome@stable

# 2. Configura .env
CHROME_PATH=/ruta/completa/proyecto/chrome/linux-XXXXXX/chrome-linux64/chrome

# 3. ¡Listo! PDF funciona sin errores
```

✅ **Esto funciona porque Chrome queda dentro del directorio permitido por `open_basedir`.**

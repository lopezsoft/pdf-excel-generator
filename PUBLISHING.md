# Guía de Publicación en GitHub y Packagist

## 📋 Pasos para Publicar la Librería

### 1. Crear Repositorio en GitHub

1. Ve a [github.com](https://github.com) y inicia sesión
2. Click en "New repository" (botón verde)
3. Configura el repositorio:
   - **Repository name:** `pdf-excel-generator`
   - **Description:** "Generate PDFs and Excel files from HTML/Blade templates or data arrays for Laravel"
   - **Visibility:** Public
   - **NO** inicializar con README (ya lo tenemos)
   - Click "Create repository"

### 2. Conectar Repositorio Local con GitHub

Ejecuta estos comandos en la terminal:

```bash
cd d:/wamp64/www/packages/pdf-excel-generator

# Agregar remote de GitHub (reemplaza 'lopezsoft' con tu usuario)
git remote add origin https://github.com/lopezsoft/pdf-excel-generator.git

# Verificar remote
git remote -v

# Push del código
git branch -M main
git push -u origin main
```

### 3. Crear Tag de Versión

```bash
# Crear tag v1.0.0
git tag -a v1.0.0 -m "Release v1.0.0 - Initial stable release"

# Push del tag
git push origin v1.0.0
```

### 4. Crear Release en GitHub

1. Ve a tu repositorio en GitHub
2. Click en "Releases" → "Create a new release"
3. Selecciona el tag `v1.0.0`
4. **Release title:** `v1.0.0 - Initial Release`
5. **Description:** Copia el contenido de CHANGELOG.md para v1.0.0
6. Click "Publish release"

### 5. Registrar en Packagist.org

1. Ve a [packagist.org](https://packagist.org)
2. **Registra una cuenta** si no tienes (usa tu email de GitHub)
3. Click en "Submit" en el menú superior
4. Ingresa la URL de tu repositorio:
   ```
   https://github.com/lopezsoft/pdf-excel-generator
   ```
5. Click "Check" → Packagist validará el composer.json
6. Si todo está OK, click "Submit"

### 6. Configurar Auto-Update (Webhook)

Para que Packagist se actualice automáticamente cuando hagas push:

1. En Packagist, ve a tu paquete → "Settings"
2. Copia la "GitHub Service Hook URL"
3. Ve a tu repositorio en GitHub → Settings → Webhooks
4. Click "Add webhook"
5. **Payload URL:** Pega la URL de Packagist
6. **Content type:** `application/json`
7. **Which events:** Just the push event
8. Click "Add webhook"

### 7. Verificar Instalación

Ahora cualquiera puede instalar tu paquete:

```bash
composer require lopezsoft/pdf-excel-generator
```

## 🔄 Workflow de Desarrollo Futuro

### Para Nuevas Versiones

1. **Hacer cambios en el código**
2. **Actualizar CHANGELOG.md**
3. **Actualizar version en composer.json**
4. **Commit y push:**
   ```bash
   git add .
   git commit -m "feat: nueva funcionalidad"
   git push origin main
   ```
5. **Crear nuevo tag:**
   ```bash
   git tag -a v1.1.0 -m "Release v1.1.0"
   git push origin v1.1.0
   ```
6. **Crear release en GitHub**
7. Packagist se actualizará automáticamente (si configuraste el webhook)

### Versionado Semántico

Sigue [SemVer](https://semver.org/):

- **MAJOR** (v2.0.0): Cambios incompatibles con versiones anteriores
- **MINOR** (v1.1.0): Nueva funcionalidad compatible
- **PATCH** (v1.0.1): Bug fixes compatibles

Ejemplos:
- `v1.0.1` - Fix de bug
- `v1.1.0` - Nueva función para generar PDFs con watermark
- `v2.0.0` - Cambio en API que rompe compatibilidad

## 📝 Conventional Commits

Usa estos prefijos en tus commits:

- `feat:` Nueva funcionalidad
- `fix:` Bug fix
- `docs:` Cambios en documentación
- `style:` Formateo de código
- `refactor:` Refactorización
- `test:` Agregar tests
- `chore:` Mantenimiento

Ejemplo:
```bash
git commit -m "feat(pdf): add watermark support"
git commit -m "fix(excel): correct date formatting"
git commit -m "docs: update README with new examples"
```

## 🎯 Estado Actual

✅ Repositorio Git inicializado
✅ Primer commit creado
✅ Código completo y funcional
✅ Tests implementados
✅ README completo
✅ CHANGELOG actualizado

**Próximos pasos:**
1. Crear repositorio en GitHub
2. Push del código
3. Crear tag v1.0.0
4. Publicar en Packagist

## 🔐 Credenciales Necesarias

- Cuenta GitHub (usuario: lopezsoft)
- Email: lopezsoft.com@gmail.com
- Cuenta Packagist (usar mismo email)

## 📞 Soporte

Si tienes problemas durante la publicación:
- [GitHub Help](https://help.github.com)
- [Packagist Help](https://packagist.org/about)

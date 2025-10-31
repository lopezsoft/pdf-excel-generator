# Contributing to PDF Excel Generator

¡Gracias por tu interés en contribuir! 🎉

## 📋 Código de Conducta

Este proyecto se adhiere a un código de conducta. Al participar, se espera que mantengas un ambiente respetuoso y profesional.

## 🚀 Cómo Contribuir

### Reportar Bugs

1. Verifica que el bug no haya sido reportado anteriormente
2. Abre un nuevo issue con:
   - Descripción clara del problema
   - Pasos para reproducirlo
   - Versión de PHP, Laravel y del paquete
   - Stack trace si es aplicable

### Sugerir Mejoras

1. Abre un issue describiendo tu sugerencia
2. Explica por qué sería útil
3. Proporciona ejemplos de uso

### Pull Requests

1. **Fork el repositorio**
2. **Crea una rama** desde `main`:
   ```bash
   git checkout -b feature/amazing-feature
   ```
3. **Implementa tus cambios**:
   - Sigue PSR-12 coding standards
   - Escribe tests para nueva funcionalidad
   - Actualiza la documentación
4. **Ejecuta tests**:
   ```bash
   composer test
   ```
5. **Commit siguiendo Conventional Commits**:
   ```bash
   git commit -m "feat: add amazing feature"
   ```
6. **Push a tu fork**:
   ```bash
   git push origin feature/amazing-feature
   ```
7. **Abre un Pull Request** en GitHub

## 📝 Estándares de Código

- **PSR-12** para estilo de código
- **Strict types** (`declare(strict_types=1)`)
- **Type hints** en todos los parámetros y retornos
- **PHPDoc** en clases y métodos públicos
- **SOLID principles**
- **Tests** para toda nueva funcionalidad

## 🧪 Testing

```bash
# Ejecutar todos los tests
composer test

# Tests con coverage
composer test -- --coverage

# Tests específicos
vendor/bin/phpunit tests/Unit/PathValidatorTest.php
```

## 📖 Documentación

Al agregar nueva funcionalidad:

1. Actualiza el **README.md** con ejemplos
2. Agrega PHPDoc completo
3. Actualiza **CHANGELOG.md** siguiendo [Keep a Changelog](https://keepachangelog.com/)

## 🔄 Proceso de Review

1. Al menos 1 aprobación requerida
2. Tests deben pasar
3. Code coverage no debe disminuir
4. Documentación debe estar actualizada

## 📊 Estructura del Proyecto

```
src/
├── Contracts/          # Interfaces
├── Exporters/          # Lógica de exportación
├── Exceptions/         # Excepciones personalizadas
├── Validators/         # Validadores
├── Results/            # Value Objects
└── Facades/            # Laravel Facades
```

## 💡 Guías

### Agregar Nuevo Exporter

1. Crear clase en `src/Exporters/`
2. Extender `AbstractExporter`
3. Implementar métodos abstractos
4. Agregar tests en `tests/Unit/`
5. Actualizar README con ejemplos

### Agregar Nueva Excepción

1. Crear en `src/Exceptions/`
2. Extender de `GeneratorException`
3. Agregar métodos estáticos factory
4. Documentar en README

## 🙏 Reconocimientos

Todos los contribuidores serán listados en el README.

Gracias por hacer este proyecto mejor! 🚀

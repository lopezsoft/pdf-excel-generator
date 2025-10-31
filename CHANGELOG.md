# Changelog

All notable changes to `pdf-excel-generator` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.2.2] - 2025-10-30

### Fixed
- **CRITICAL:** Background colors and images now render correctly in PDFs
- Added `showBackground(true)` to Browsershot configuration (was missing)
- HTML elements with `background-color`, `background-image`, etc. now display properly

### Added
- `printBackground(bool)` method to enable/disable background printing
- Backgrounds enabled by default for better visual fidelity
- Tests for background rendering (`BackgroundPrintTest`)
- README documentation for background printing with examples

### Changed
- PDFs now render backgrounds by default (matches browser print behavior)
- Can be disabled with `->printBackground(false)` if needed

## [1.2.1] - 2025-10-30

### Fixed
- **CRITICAL:** Fixed subdirectory creation when saving files with paths like `1/file.pdf`
- `PathValidator::sanitizeFilename()` now correctly preserves directory structure
- Files are now properly saved in nested subdirectories (e.g., `invoices/2025/10/invoice.pdf`)
- Handles both forward slashes (`/`) and backslashes (`\`) in paths
- Automatic directory creation now works for all export methods (PDF and Excel)

### Added
- Test for numeric subdirectory creation (`1/uuid.pdf` pattern)
- Tests for `sanitizeFilename()` subdirectory preservation
- Tests for backslash path handling

## [1.2.0] - 2025-10-30

### Added
- Chrome Pool configuration section in `config/pdf-excel-generator.php`
- New configuration options for Chrome Pool:
  - `chrome_pool.enabled` - Enable/disable Chrome Pool
  - `chrome_pool.debug_port` - Custom debug port (default: auto-select)
  - `chrome_pool.startup_timeout` - Timeout for Chrome startup (default: 5s)
  - `chrome_pool.connection_retries` - Connection retry attempts (default: 3)
  - `chrome_pool.auto_restart` - Auto-restart on crash (default: true)
- Environment variables support:
  - `CHROME_POOL_ENABLED`
  - `CHROME_POOL_DEBUG_PORT`
  - `CHROME_POOL_STARTUP_TIMEOUT`
  - `CHROME_POOL_CONNECTION_RETRIES`
  - `CHROME_POOL_AUTO_RESTART`
- `isEnabled()` method in `ChromePool` to check if pool is enabled
- Comprehensive configuration documentation in README
- Configuration table with all options and defaults

### Improved
- Chrome Pool now respects configuration settings
- Better error handling with configurable retries
- More control over pool behavior without code changes
- Prevents accidental pool usage in low-memory environments
- Production-ready with sensible defaults

### Changed
- Chrome Pool is now **disabled by default** (must be explicitly enabled)
- `start()` method now throws exception if pool is disabled in config

## [1.1.1] - 2025-10-30

### Added
- Automatic directory creation when saving files with subdirectories
- `ensureDirectoryExists()` method in `AbstractExporter` for recursive directory creation
- `DirectoryCreationTest` unit tests (8 test cases)
- README section explaining automatic directory creation

### Fixed
- Files can now be saved in nested paths without manually creating directories (e.g., `savePdf('docs/invoices/2025/invoice.pdf')`)
- Prevents "directory not found" errors when using subdirectories
- Works seamlessly with all configured disks (local, s3, public, etc.)

## [1.1.0] - 2025-10-30

### Added
- HTML normalization to reduce PDF size by 20-30% (inspired by CustomPdf)
- PDF header validation (`%PDF`) to detect corruption early
- `InvalidPdfException` for better error handling
- Custom margins support with `margins()` and `customMargins()` methods
- `ChromePool` service for high-concurrency scenarios (reduces generation time from 4s to 1.5s)
- `ChromePoolInterface` contract for extensibility
- Comprehensive unit tests (`PdfValidationTest` and `ExcelValidationTest`)
- Template Method pattern for content validation in `AbstractExporter`

### Improved
- `PdfExporter` now normalizes HTML before rendering (removes excessive whitespace from Blade templates)
- `AbstractExporter::save()` now validates generated content before saving
- README with optimization guidelines and Chrome Pool usage documentation
- Better error messages with hex dump for corrupted PDFs

### Fixed
- PDF corruption detection now happens before file is saved to disk
- Improved margin configuration (individual top/right/bottom/left support)

## [1.0.0] - 2025-10-30

### Added
- Initial release
- PDF generation from HTML using spatie/browsershot
- PDF generation from Blade templates
- Excel generation from data arrays using PhpSpreadsheet
- Fluent API for chaining operations
- Support for multiple storage disks
- URL generation for downloads
- Stream and download capabilities
- Comprehensive validation (path security, template existence)
- Custom exceptions hierarchy
- Laravel 9.x, 10.x, and 11.x support
- PHP 8.1+ support
- Complete test suite (unit and feature tests)
- PSR-4 autoloading
- PSR-12 code style

[Unreleased]: https://github.com/lopezsoft/pdf-excel-generator/compare/v1.1.1...HEAD
[1.1.1]: https://github.com/lopezsoft/pdf-excel-generator/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/lopezsoft/pdf-excel-generator/releases/tag/v1.1.0
[1.0.0]: https://github.com/lopezsoft/pdf-excel-generator/releases/tag/v1.0.0

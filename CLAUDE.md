# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

composer-unused is a static analysis tool that identifies unused Composer dependencies by scanning PHP code for symbol usage. It analyzes composer.json requirements against actual code usage to detect packages that can be safely removed.

## Key Architecture Components

### Core Analysis Flow
1. **Symbol Collection**: `Symbol/` namespace handles building loaders for consumed and provided symbols
2. **Dependency Resolution**: `Dependency/` manages required dependencies and collections
3. **Composer Integration**: `Composer/` provides package analysis, local repository handling, and installed package parsing
4. **Command Processing**: `Command/` contains CQRS-style commands and handlers for symbol collection and dependency loading
5. **Output Formatting**: `OutputFormatter/` supports multiple output formats (default, compact, JSON, JUnit, GitHub, GitLab)

### Configuration System
- Main config via `composer-unused.php` file (see Configuration class)
- Supports named filters (`NamedFilter`) and pattern-based filters (`PatternFilter`) for excluding packages
- Additional file parsing can be configured per dependency
- CLI options for excluding directories and packages

### Console Commands
- `UnusedCommand`: Main command for finding unused dependencies
- `DebugConsumedSymbolsCommand`: Debug consumed symbols in code
- `DebugProvidedSymbolsCommand`: Debug symbols provided by packages

## Development Commands

### Local Development (Docker recommended)
```bash
# Setup
make up                    # Start Docker containers
make install              # Install dependencies
make clean                # Clean vendor/ and composer.lock

# Testing and Quality
make check                # Run all checks (cs, analyse, phpunit)
make phpunit              # Run PHPUnit tests
make analyse              # Run PHPStan analysis  
make cs                   # Run PHP CodeSniffer
make csfix                # Fix coding standards

# Building
make box                  # Compile PHAR archive
```

### Composer Scripts
```bash
composer test             # Run PHPUnit tests
composer analyse          # Run PHPStan analysis (level 8)
composer cs-check         # Check coding standards
composer cs-fix           # Fix coding standards automatically
composer check            # Run all quality checks
```

### Testing
- PHPUnit configuration in `phpunit.xml`
- Tests organized in `tests/Integration/` and `tests/Unit/`
- Test assets in `tests/assets/TestProjects/` for integration testing
- Run specific test suites: `vendor/bin/phpunit --testsuite=unit`

### Static Analysis
- PHPStan level 8 with configuration in `phpstan.neon`
- Baseline file: `phpstan-baseline.neon`
- Excludes test assets directory from analysis

### Code Standards
- PHP_CodeSniffer configuration in `phpcs.xml`
- Parallel processing with caching enabled
- Use `composer cs-fix` to automatically fix most issues

## Key Implementation Notes

### Symbol Parser Integration
Uses `composer-unused/symbol-parser` for PHP code analysis and symbol extraction.

### Composer Version Support
- Requires `composer-runtime-api: ^2.0`
- Handles different installed.json formats via `SupportedInstalledPackagesVersionChecker`

### Package Resolution
- `PackageResolver` manages dependency resolution logic
- `LocalRepository` and `LocalRepositoryFactory` handle local package analysis
- Special handling for packages without proper autoload configuration

### Output Flexibility
Multiple output formatters support different CI/CD integrations and development workflows.

## Build Process

The project supports building a PHAR archive using Box:
- Configuration in `box.json`
- Scoped dependencies to avoid conflicts
- GPG signing for distribution
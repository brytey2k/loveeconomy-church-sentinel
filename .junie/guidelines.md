# Development Guidelines

This document provides guidelines for development on the Love Economy USSD project. It is intended for advanced developers and includes project-specific information.

## Build/Configuration Instructions

### Environment Setup

1. **PHP Requirements**: PHP 8.2+ is required for this project.

2. **Dependencies Installation**:
   ```bash
   composer install
   npm install
   ```

3. **Environment Configuration**:
   - Copy `.env.example` to `.env`
   - Configure your database settings in `.env`
   - Generate application key: `php artisan key:generate`

4. **Database Setup**:
   - This project uses PostgreSQL
   - Create a database matching your `.env` configuration
   - Run migrations: `php artisan migrate`
   - Seed the database (if needed): `php artisan db:seed`

### Docker Development Environment

The project uses Laravel Sail for Docker-based development:

1. **Start the environment**:
   ```bash
   ./vendor/bin/sail up -d
   ```

2. **Docker services**:
   - PHP 8.4 application server
   - PostgreSQL 17 database
   - Redis for caching/queues

3. **Port mappings**:
   - Application: `${APP_PORT:-80}:80`
   - Vite dev server: `${VITE_PORT:-5173}:${VITE_PORT:-5173}`
   - PostgreSQL: `${FORWARD_DB_PORT:-5432}:5432`
   - Redis: `${FORWARD_REDIS_PORT:-6379}:6379`

4. **Development command**:
   ```bash
   composer dev
   ```
   This runs the server, queue listener, logs, and Vite concurrently.

## Testing Information

### Test Structure

Tests are organized into four categories:
- **Unit Tests**: For testing individual components in isolation
- **Feature Tests**: For testing application features via HTTP requests
- **Integration Tests**: For testing interactions between components
- **Contract Tests**: For testing API contracts

### Running Tests

1. **Run all tests**:
   ```bash
   vendor/bin/phpunit
   ```

2. **Run specific test suite**:
   ```bash
   vendor/bin/phpunit --testsuite=Unit
   vendor/bin/phpunit --testsuite=Feature
   vendor/bin/phpunit --testsuite=Integration
   vendor/bin/phpunit --testsuite=Contract
   ```

3. **Run specific test file**:
   ```bash
   vendor/bin/phpunit tests/Unit/GuidelinesDemoTest.php
   ```

4. **Run specific test method**:
   ```bash
   vendor/bin/phpunit --filter=test_string_concatenation tests/Unit/GuidelinesDemoTest.php
   ```

### Adding New Tests

1. **Create a new test file** in the appropriate directory:
   - Unit tests: `tests/Unit/`
   - Feature tests: `tests/Feature/`
   - Integration tests: `tests/Integration/`
   - Contract tests: `tests/Contract/`

2. **Test naming conventions**:
   - Test classes should be named with a `Test` suffix
   - Test methods should use snake_case and be prefixed with `test_`
   - Example: `public function test_user_can_login(): void`

3. **Test example**:

```php
<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class GuidelinesDemoTest extends TestCase
{
    /**
     * A simple test to demonstrate the testing process.
     */
    public function test_string_concatenation(): void
    {
        $string1 = 'Hello';
        $string2 = 'World';
        
        $result = $string1 . ' ' . $string2;
        
        $this->assertEquals('Hello World', $result);
    }
}
```

### Testing Database

Tests use a dedicated PostgreSQL database named `lec_sentinel_testing`. This database is automatically created when using Laravel Sail.

## Code Style and Development Practices

### Code Style

The project uses Laravel Pint for code style enforcement with the following key rules:
- PSR-12 as the base preset
- Strict types declaration required
- Single quotes for strings
- Short array syntax
- Snake case for test methods
- Specific PHPDoc formatting rules

### Static Analysis

PHPStan is used for static analysis:
- Level 5 (out of 9) is currently used
- Analyzes both app and tests directories

### Development Commands

1. **Code style check**:
   ```bash
   composer pint-test
   ```

2. **Code style fix**:
   ```bash
   composer pint-fix
   ```

3. **Static analysis**:
   ```bash
   composer phpstan
   ```

4. **Generate IDE helper files**:
   ```bash
   composer ide-helper
   ```

### Mutation Testing

The project uses Infection for mutation testing, which helps ensure test quality by modifying your code and checking if tests detect the changes.

### PostgreSQL Features

The project uses enhanced PostgreSQL features:
- LTree extension for hierarchical data (via `umbrellio/laravel-ltree`)
- Enhanced PostgreSQL features (via `tpetry/laravel-postgresql-enhanced`)

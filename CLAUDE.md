# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Laravel 13 API for a simplified *Portal da Transparência* (Transparency Portal). Exposes endpoints for public spending data — organs (secretarias), suppliers (fornecedores), and expenses (despesas).

**Current state**: All business logic lives in `routes/api.php` as closures. A planned refactor would extract into Controllers, API Resources, and Form Requests.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

API runs at `http://localhost:8000/api`.

`API_KEYS` must be set in `.env` before seeding, otherwise the seeder generates no data:
```
API_KEYS=candidate1:secret-key-1,candidate2:secret-key-2
```

## Common Commands

```bash
# Run all tests
composer test
# or
php artisan test

# Run a single test file
php artisan test tests/Feature/ExampleTest.php

# Run a specific test by name
php artisan test --filter="test name"

# Code linting (Laravel Pint)
./vendor/bin/pint

# Lint dry-run
./vendor/bin/pint --test
```

## Architecture

### Multi-tenancy

Every API request must include `X-API-Key: <key>`. `ApiKeyMiddleware` (applied to all `api` routes in `bootstrap/app.php`) validates the key against the `API_KEYS` env variable and resolves the alias as `tenant_id` via `app()->instance('tenant_id', $alias)`.

`API_KEYS` format: comma-separated `alias:secret` pairs — e.g. `candidate1:abc123,candidate2:xyz789`.

All three models (`Orgao`, `Fornecedor`, `Despesa`) use an Eloquent global scope that automatically filters queries by `tenant_id` and sets `tenant_id` on creation — **every model query is tenant-scoped automatically**. To bypass scoping (e.g., in seeders), use `Model::withoutGlobalScopes()`.

### Database (SQLite)

Three domain models:
- `Orgao` → hasMany `Despesa`
- `Fornecedor` → hasMany `Despesa`
- `Despesa` → belongsTo `Orgao`, belongsTo `Fornecedor`

Unique constraints are **per-tenant**: `(tenant_id, name)` for `orgaos`, `(tenant_id, document)` for `fornecedores`. Validation rules in routes use Laravel's column-level unique rule and must ignore the current record by id on updates.

### File Storage

Comprovantes (expense receipts) are stored on the `comprovantes` disk, rooted at `COMPROVANTES_PATH` env (defaults to `database/uploads`). Accepted formats: jpeg, png, pdf, max 5 MB.

### Testing

Uses **Pest**. `RefreshDatabase` is commented out in `tests/Pest.php` — enable it when tests need a clean DB state.

### Route Order

Static routes must be declared **before** wildcard `{id}` routes to avoid capture. This applies to:
- `/orgaos/paginado` before `/orgaos/{id}`
- `/fornecedores/paginado` before `/fornecedores/{id}`
- `/despesas/paginado`, `/despesas/total/orgao`, `/despesas/total/fornecedor`, and `/despesas/{id}/comprovante` before `/despesas/{id}`

### Pending Implementations

Two routes exist but return empty responses:
- `GET /api/despesas/total/orgao` — total spending grouped by organ
- `GET /api/despesas/total/fornecedor` — total spending grouped by supplier

### Refactoring Target Structure

When extracting from `routes/api.php`:
- `app/Http/Controllers/` — `OrgaoController`, `FornecedorController`, `DespesaController`
- `app/Http/Resources/` — API Resources/Collections for each model
- `app/Http/Requests/` — Form Requests for validation

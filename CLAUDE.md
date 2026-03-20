# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **Laravel 13 API** for a simplified *Portal da Transparência* (Transparency Portal). It exposes endpoints for public spending data — organs (secretarias), suppliers (fornecedores), and expenses (despesas).

**Current state**: All business logic lives in `routes/api.php` as closures. The task is to refactor into Controllers, API Resources, and Form Requests.

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

### Database (SQLite)

Three domain models with these relationships:
- `Orgao` → hasMany `Despesa`
- `Fornecedor` → hasMany `Despesa`
- `Despesa` → belongsTo `Orgao`, belongsTo `Fornecedor`

### Testing

Uses **Pest** (not PHPUnit directly). Tests live in `tests/Feature/` and `tests/Unit/`. The `RefreshDatabase` trait is commented out in `tests/Pest.php` — enable it if tests need a clean DB state.

### Refactoring Target Structure

When extracting from `routes/api.php`, the intended structure is:
- `app/Http/Controllers/` — `OrgaoController`, `FornecedorController`, `DespesaController`
- `app/Http/Resources/` — API Resources/Collections for each model
- `app/Http/Requests/` — Form Requests for validation

### Pending Implementations

Two routes exist but return empty responses — they need implementation:
- `GET /api/despesas/total/orgao` — total spending grouped by organ
- `GET /api/despesas/total/fornecedor` — total spending grouped by supplier

Despesas listing also needs additional filters beyond the existing `orgao_id`: `fornecedor_id`, `valor_min`, `valor_max`.

### Route Order Note

In `routes/api.php`, the static routes `/despesas/total/orgao` and `/despesas/total/fornecedor` are declared **before** `/despesas/{id}` — this ordering is intentional to prevent the `{id}` wildcard from capturing "total".

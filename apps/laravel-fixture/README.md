# laravel-fixture

Локальное приложение **Laravel 12** для разработки `hybrid/laravel-hybrid-updater` (path-зависимость в `composer.json`).

Общее описание и запуск: [корневой README.md](../../README.md) · [English](../../README.en.md)

## Кратко

```bash
composer install
cp .env.example .env   # при необходимости
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan serve --host=127.0.0.1 --port=8080
```

Скрипты смока: `scripts/create-fixtures.ps1`, `scripts/smoke-checks.sh`.

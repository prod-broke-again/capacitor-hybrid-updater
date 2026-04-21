# hybrid/laravel-hybrid-updater

Laravel package: hybrid (native + web) update checks and artifact uploads.

**RU / EN (overview):** [README.md](../../README.md) · [README.en.md](../../README.en.md)

**Laravel:** 11.x / 12.x / 13.x — [laravel-compatibility.md](../../docs/laravel-compatibility.md)

## HTTP surface

- `GET {route_prefix}/check`
- `POST {route_prefix}/web-bundles` (token middleware `web`)
- `POST {route_prefix}/android/releases` (token middleware `android`)

Default `route_prefix`: `api/updater` (`config/hybrid-updater.php`).

## Install

```bash
composer require hybrid/laravel-hybrid-updater
php artisan vendor:publish --tag=hybrid-updater-config
php artisan vendor:publish --tag=hybrid-updater-migrations
php artisan migrate
```

Set `HYBRID_UPDATER_ANDROID_UPLOAD_TOKEN` and `HYBRID_UPDATER_WEB_UPLOAD_TOKEN` in `.env`. Configure filesystem disks named in config (`web-bundles`, `android-releases`).

## Development (this monorepo)

```bash
composer test
composer analyse
```

# capacitor-hybrid-updater

**Android-first** monorepo for Capacitor app updates: a Capacitor plugin (TypeScript + Kotlin), a Laravel package for OTA/APK metadata, and update providers `laravel` and `githubReleases`.

**Русский:** [README.md](README.md)

## Layout

| Path | Purpose |
|------|---------|
| [packages/capacitor-hybrid-updater](packages/capacitor-hybrid-updater) | Capacitor plugin, provider router, Android runtime |
| [packages/laravel-hybrid-updater](packages/laravel-hybrid-updater) | Laravel package: `GET /check`, web-bundle and APK uploads |
| [apps/ionic-demo](apps/ionic-demo) | Ionic React + Capacitor demo |
| [apps/laravel-fixture](apps/laravel-fixture) | Local Laravel 12 app for package development |
| [docs](docs) | API contracts, HTTP reference, migration notes |

Laravel support for the package: **11 / 12 / 13** — [docs/laravel-compatibility.md](docs/laravel-compatibility.md).

## Requirements

- PHP 8.2+, Composer  
- Node 20+, npm  
- For Android demo: Android Studio, JDK 17+

## Quick start

**Backend fixture (default `http://127.0.0.1:8080`):**

```bash
cd apps/laravel-fixture
composer install
php artisan serve --host=127.0.0.1 --port=8080
```

**Demo client (from monorepo root):**

```bash
npm install
cd apps/ionic-demo
npm run build
npx cap sync android
```

For the Android emulator set `VITE_LARAVEL_BASE_URL=http://10.0.2.2:8080` in the demo `.env` (host loopback).

**Laravel package tests:**

```bash
cd packages/laravel-hybrid-updater
composer test
composer analyse
```

## Docs

- [Package HTTP API](docs/api-updater.md)  
- [Laravel compatibility](docs/laravel-compatibility.md)  
- [Manifest and releases](docs/provider-manifest-and-release.md)

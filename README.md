# capacitor-hybrid-updater

Монорепозиторий **Android-first** стека обновлений для Capacitor: плагин (TypeScript + Kotlin), Laravel-пакет для метаданных OTA/APK и провайдеры `laravel` и `githubReleases`.

**English:** [README.en.md](README.en.md)

## Состав

| Путь | Назначение |
|------|------------|
| [packages/capacitor-hybrid-updater](packages/capacitor-hybrid-updater) | Capacitor-плагин, роутер провайдеров, Android runtime |
| [packages/laravel-hybrid-updater](packages/laravel-hybrid-updater) | Laravel-пакет: `GET /check`, загрузки web-bundle и APK |
| [apps/ionic-demo](apps/ionic-demo) | Демо Ionic React + Capacitor |
| [apps/laravel-fixture](apps/laravel-fixture) | Локальное приложение Laravel 12 для разработки пакета |
| [docs](docs) | Контракты API, HTTP, миграция с legacy |

Совместимость Laravel для пакета: **11 / 12 / 13** — [docs/laravel-compatibility.md](docs/laravel-compatibility.md).

## Требования

- PHP 8.2+, Composer  
- Node 20+, npm  
- Для Android-демо: Android Studio, JDK 17+

## Быстрый старт

**Бэкенд (фикстура), по умолчанию `http://127.0.0.1:8080`:**

```bash
cd apps/laravel-fixture
composer install
php artisan serve --host=127.0.0.1 --port=8080
```

**Демо-клиент (из корня монорепо):**

```bash
npm install
cd apps/ionic-demo
npm run build
npx cap sync android
```

Для эмулятора задайте в `.env` демо `VITE_LARAVEL_BASE_URL=http://10.0.2.2:8080` (доступ к хосту).

**Тесты Laravel-пакета:**

```bash
cd packages/laravel-hybrid-updater
composer test
composer analyse
```

## Документация

- [HTTP API пакета](docs/api-updater.md)  
- [Совместимость Laravel](docs/laravel-compatibility.md)  
- [Манифест и релизы](docs/provider-manifest-and-release.md)

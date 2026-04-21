# Laravel version compatibility

**README:** [RU](../README.md) · [EN](../README.en.md)

The package `hybrid/laravel-hybrid-updater` declares:

```text
illuminate/support: ^11.0|^12.0|^13.0
```

Use the **same major** for `laravel/framework` (and other Illuminate components) as in your application:

| Laravel app | Illuminate / `laravel/framework` |
|-------------|----------------------------------|
| 11.x        | `^11.0`                          |
| 12.x        | `^12.0`                          |
| 13.x        | `^13.0`                          |

PHP requirements follow the [Laravel server requirements](https://laravel.com/docs/installation) for your chosen version (newer Laravel releases may require a newer PHP minimum).

## Local development in this monorepo

- **`apps/laravel-fixture`** is pinned to **Laravel 12** (`laravel/framework: ^12.0`) as the default integration app.
- Package behaviour is validated with PHPUnit against the **Domain** layer; run tests in `packages/laravel-hybrid-updater` with `composer test`.

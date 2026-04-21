# Hybrid Updater API (Laravel package)

Base path: `config('hybrid-updater.route_prefix')`, default `api/updater` (full URL: `{APP_URL}/api/updater/...` when routes are registered at the application root).

## GET `/check`

Query parameters:

| Parameter | Description |
|-----------|-------------|
| `channel` | default `stable` |
| `platform` | optional: `web` or `android` |
| `native_version` | optional client native version string |
| `native_build` | optional client native build int |
| `web_version` | optional current web bundle version on client |

Response JSON:

- `meta`: `channel`, `checkedAt`, `source`
- `current`: echo of client query (`nativeVersion`, `nativeBuild`, `webVersion`)
- `decision`: `hasUpdate`, `updateType`, `isCompatible`, `reason`
- `manifest`: normalized manifest object or `null`

## POST `/web-bundles`

Multipart upload; requires `hybrid-updater.upload-token:web`.

## POST `/android/releases`

Multipart upload; requires `hybrid-updater.upload-token:android`.

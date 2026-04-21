# Migration Guide: `ionic-vue-tabs` + `psy` -> Hybrid Updater v1

## Client migration (`ionic-vue-tabs`)

1. Replace direct `getAppVersion` + custom compare logic from `useOtaUpdates` with `@hybrid/capacitor-hybrid-updater`.
2. Keep existing UI components (modal/toast) in app layer.
3. Map old fields:
   - `native.latest_version` -> manifest version + `apk` artifact.
   - `native.build_number` -> policy in host app (force install prompts).
   - `web.version/url/min_native_*` -> manifest `web_bundle` + compatibility fields.
4. Initialize plugin in app bootstrap and subscribe to events:
   - `onUpdateAvailable`
   - `onDownloadProgress`
   - `onInstallRequired`
   - `onError`

## Backend migration (`psy`)

1. Install `hybrid/laravel-hybrid-updater` package in host Laravel app.
2. Publish package config and migrations.
3. Copy existing OTA data into new tables:
   - `web_bundles`
   - `android_releases`
4. Point clients at `GET {route_prefix}/check` and the upload routes under the same prefix (see [api-updater.md](./api-updater.md)).
5. Move upload tokens to:
   - `HYBRID_UPDATER_ANDROID_UPLOAD_TOKEN`
   - `HYBRID_UPDATER_WEB_UPLOAD_TOKEN`

## Cutover checklist

- Validate no-update path.
- Validate web-only update path.
- Validate apk-required path.
- Validate incompatible-native path.

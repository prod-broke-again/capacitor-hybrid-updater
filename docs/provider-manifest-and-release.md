# Provider Manifest and Release Notes

## Unified `manifest.json`

Each release must publish `manifest.json` with:

- `version`
- `releaseDate`
- `channel`
- `updateType`
- `minNativeVersion`
- `minNativeBuild`
- `artifacts[]` (`apk` and/or `web_bundle` with checksum)

## Laravel provider

- Endpoint: `GET {route_prefix}/check` (default prefix `api/updater`).
- Upload endpoints:
  - `POST {route_prefix}/web-bundles`
  - `POST {route_prefix}/android/releases`

## GitHub Releases provider

- Uses `releases/latest`.
- Requires `manifest.json` as release asset.
- APK and ZIP assets must match artifact URLs declared in manifest.

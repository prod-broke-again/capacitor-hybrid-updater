# E2E Scenarios (v1)

## Scope

Environment:

- `apps/ionic-demo` as client app.
- `apps/laravel-fixture` as controlled backend.
- Android device/emulator for native runtime checks.

## Scenario Matrix

### 1) No update

- Backend returns latest manifest equal to client versions.
- Expected:
  - `checkForUpdate` returns `hasUpdate = false`.
  - No `onInstallRequired` event.

### 2) Web update available

- Backend serves manifest with newer `version` and `web_bundle` artifact.
- Expected:
  - `downloadUpdate` succeeds.
  - `applyWebUpdate` stores next bundle and reports `willActivateOnRestart = true`.
  - After app restart, runtime activates pending bundle and `getCurrentState().currentWebBundleVersion` changes.

### 3) APK update available

- Backend serves manifest with `apk` artifact and higher native build.
- Expected:
  - `downloadApk` emits progress and returns local file path.
  - `installApk` starts Android installer intent.

### 4) Incompatible native for web bundle

- Backend sets `minNativeVersion`/`minNativeBuild` above current app values.
- Expected:
  - `checkForUpdate` returns `compatibility.isCompatible = false`.
  - Host app can react with force APK prompt policy.

## Manual Runbook

1. Start Laravel fixture server.
2. Upload fixture artifacts by running `apps/laravel-fixture/scripts/smoke-checks.sh`.
3. Launch Ionic demo on Android.
4. Trigger `checkForUpdates` from demo UI.
5. Validate events and state transitions in logcat and app UI.

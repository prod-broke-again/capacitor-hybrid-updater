# Capacitor Hybrid Updater Plugin API (v1)

## Methods

- `configure(options: UpdaterConfig): Promise<void>`
- `getCurrentState(): Promise<UpdaterState>`
- `checkForUpdate(options: CheckForUpdateOptions): Promise<UpdateCheckResult>`
- `downloadUpdate(options: DownloadUpdateOptions): Promise<DownloadResult>`
- `applyWebUpdate(options: ApplyWebUpdateOptions): Promise<ApplyWebUpdateResult>`
- `downloadApk(options: DownloadApkOptions): Promise<DownloadResult>`
- `installApk(options: InstallApkOptions): Promise<InstallApkResult>`
- `resetToBuiltin(): Promise<ResetResult>`

## Events

- `onUpdateAvailable`: emitted when check returns an applicable candidate.
- `onDownloadProgress`: emitted with progress details for web/apk artifacts.
- `onInstallRequired`: emitted when update policy requires APK install.
- `onError`: emitted for normalized runtime/provider errors.

## Principles

- Provider-specific responses must be normalized before leaving core.
- UI is not included in plugin scope. Host app decides UX.
- `manifest.json` is mandatory for both `laravel` and `githubReleases`.
- Android is the only supported native runtime in v1.

export type UpdateMode = "apk" | "web" | "hybrid";
export type UpdateProvider = "laravel" | "githubReleases";
export type UpdateType = "ota_web_only" | "apk_required" | "apk_or_ota";
export type ArtifactType = "apk" | "web_bundle" | "manifest";

export interface ManifestArtifact {
  type: ArtifactType;
  url: string;
  checksum: string;
  size?: number;
}

export interface NormalizedManifest {
  version: string;
  releaseDate: string;
  channel: string;
  updateType: UpdateType;
  minNativeVersion: string;
  minNativeBuild: number;
  notes?: string;
  artifacts: ManifestArtifact[];
  meta?: Record<string, unknown>;
}

export interface CompatibilityInfo {
  isCompatible: boolean;
  reason?: "incompatible_native" | "channel_mismatch";
}

export interface UpdaterState {
  provider: UpdateProvider;
  channel: string;
  currentNativeVersion: string;
  currentNativeBuild: number;
  currentWebBundleVersion: string;
  activeWebBundlePath?: string;
}

export interface UpdateCheckResult {
  provider: UpdateProvider;
  mode: UpdateMode;
  channel: string;
  manifest?: NormalizedManifest;
  compatibility: CompatibilityInfo;
  hasUpdate: boolean;
}

export interface DownloadResult {
  artifactType: ArtifactType;
  localPath: string;
  checksumVerified: boolean;
}

export interface ApplyWebUpdateResult {
  appliedVersion: string;
  willActivateOnRestart: boolean;
}

export interface InstallApkResult {
  started: boolean;
}

export interface ResetResult {
  reset: boolean;
}

export interface UpdaterConfig {
  provider: UpdateProvider;
  channel: string;
  mode: UpdateMode;
  providerOptions: Record<string, unknown>;
  requestTimeoutMs?: number;
}

export interface CheckForUpdateOptions {
  mode: UpdateMode;
  channel: string;
}

export interface DownloadUpdateOptions {
  manifest: NormalizedManifest;
  artifactType: ArtifactType;
}

export interface ApplyWebUpdateOptions {
  bundlePath: string;
  expectedVersion: string;
}

export interface DownloadApkOptions {
  manifest: NormalizedManifest;
}

export interface InstallApkOptions {
  apkPath: string;
}

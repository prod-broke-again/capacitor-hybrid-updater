import {
  type ApplyWebUpdateOptions,
  type ApplyWebUpdateResult,
  type ArtifactType,
  type CheckForUpdateOptions,
  type DownloadApkOptions,
  type DownloadResult,
  type DownloadUpdateOptions,
  type InstallApkOptions,
  type InstallApkResult,
  type ResetResult,
  type UpdateCheckResult,
  type UpdaterConfig,
  type UpdaterState
} from "./contracts";
import { UpdaterError } from "./errors";
import { getArtifactByType } from "./manifest";
import type { UpdateProviderContract } from "./provider";
import { ProviderRouter } from "./providerRouter";
import { compareVersions } from "./version";

interface NativeBridge {
  getCurrentState(): Promise<UpdaterState>;
  downloadArtifact(url: string, artifactType: ArtifactType, checksum?: string): Promise<DownloadResult>;
  applyWebBundle(options: ApplyWebUpdateOptions): Promise<ApplyWebUpdateResult>;
  installApk(options: InstallApkOptions): Promise<InstallApkResult>;
  resetToBuiltin(): Promise<ResetResult>;
}

export class UpdaterCore {
  private config?: UpdaterConfig;

  public constructor(
    private readonly router: ProviderRouter,
    private readonly bridge: NativeBridge
  ) {}

  public registerProvider(provider: UpdateProviderContract): void {
    this.router.register(provider);
  }

  public async configure(config: UpdaterConfig): Promise<void> {
    if (!config.channel || !config.provider || !config.mode) {
      throw new UpdaterError("CONFIG_INVALID", "Missing required updater configuration.");
    }
    this.config = config;
  }

  public async getCurrentState(): Promise<UpdaterState> {
    return this.bridge.getCurrentState();
  }

  public async checkForUpdate(options: CheckForUpdateOptions): Promise<UpdateCheckResult> {
    const config = this.requireConfig();
    const state = await this.bridge.getCurrentState();
    const provider = this.router.resolve(config.provider);
    const providerResult = await provider.checkLatest({
      mode: options.mode,
      channel: options.channel,
      current: state
    });

    const manifest = providerResult.manifest;
    if (providerResult.serverDecision) {
      const sd = providerResult.serverDecision;
      if (!manifest) {
        return {
          provider: config.provider,
          mode: options.mode,
          channel: options.channel,
          compatibility: sd.isCompatible
            ? { isCompatible: true }
            : { isCompatible: false, reason: sd.reason ?? "incompatible_native" },
          hasUpdate: false
        };
      }
      return {
        provider: config.provider,
        mode: options.mode,
        channel: options.channel,
        manifest,
        compatibility: sd.isCompatible
          ? { isCompatible: true }
          : { isCompatible: false, reason: sd.reason ?? "incompatible_native" },
        hasUpdate: sd.hasUpdate && sd.isCompatible
      };
    }

    if (!manifest) {
      return {
        provider: config.provider,
        mode: options.mode,
        channel: options.channel,
        compatibility: { isCompatible: true },
        hasUpdate: false
      };
    }

    const versionIsNewer = compareVersions(manifest.version, state.currentWebBundleVersion) > 0;
    const nativeCompatible =
      compareVersions(state.currentNativeVersion, manifest.minNativeVersion) >= 0 &&
      state.currentNativeBuild >= manifest.minNativeBuild;

    return {
      provider: config.provider,
      mode: options.mode,
      channel: options.channel,
      manifest,
      compatibility: nativeCompatible ? { isCompatible: true } : { isCompatible: false, reason: "incompatible_native" },
      hasUpdate: versionIsNewer && nativeCompatible
    };
  }

  public async downloadUpdate(options: DownloadUpdateOptions): Promise<DownloadResult> {
    const artifact = getArtifactByType(options.manifest, options.artifactType);
    if (!artifact) {
      throw new UpdaterError("MANIFEST_INVALID", `Manifest does not include ${options.artifactType} artifact.`);
    }
    return this.bridge.downloadArtifact(artifact.url, options.artifactType, artifact.checksum);
  }

  public async downloadApk(options: DownloadApkOptions): Promise<DownloadResult> {
    const artifact = getArtifactByType(options.manifest, "apk");
    if (!artifact) {
      throw new UpdaterError("MANIFEST_INVALID", "Manifest does not include APK artifact.");
    }
    return this.bridge.downloadArtifact(artifact.url, "apk", artifact.checksum);
  }

  public async applyWebUpdate(options: ApplyWebUpdateOptions): Promise<ApplyWebUpdateResult> {
    return this.bridge.applyWebBundle(options);
  }

  public async installApk(options: InstallApkOptions): Promise<InstallApkResult> {
    return this.bridge.installApk(options);
  }

  public async resetToBuiltin(): Promise<ResetResult> {
    return this.bridge.resetToBuiltin();
  }

  private requireConfig(): UpdaterConfig {
    if (!this.config) {
      throw new UpdaterError("CONFIG_INVALID", "Plugin is not configured.");
    }
    return this.config;
  }
}

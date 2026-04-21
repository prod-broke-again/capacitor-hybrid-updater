import type {
  ApplyWebUpdateOptions,
  ApplyWebUpdateResult,
  CheckForUpdateOptions,
  DownloadApkOptions,
  DownloadResult,
  DownloadUpdateOptions,
  InstallApkOptions,
  InstallApkResult,
  ResetResult,
  UpdateCheckResult,
  UpdaterConfig,
  UpdaterState
} from "./contracts";
import type { HybridUpdaterPlugin } from "./definitions";
import { WebPlugin, type PluginListenerHandle } from "@capacitor/core";
import { UpdaterError } from "./errors";

export class HybridUpdaterWeb extends WebPlugin implements HybridUpdaterPlugin {
  public async configure(_options: UpdaterConfig): Promise<void> {}

  public async getCurrentState(): Promise<UpdaterState> {
    throw new UpdaterError("UNSUPPORTED_PLATFORM", "Web runtime is not supported for hybrid updater v1.");
  }

  public async checkForUpdate(_options: CheckForUpdateOptions): Promise<UpdateCheckResult> {
    throw new UpdaterError("UNSUPPORTED_PLATFORM", "Web runtime is not supported for hybrid updater v1.");
  }

  public async downloadUpdate(_options: DownloadUpdateOptions): Promise<DownloadResult> {
    throw new UpdaterError("UNSUPPORTED_PLATFORM", "Web runtime is not supported for hybrid updater v1.");
  }

  public async applyWebUpdate(_options: ApplyWebUpdateOptions): Promise<ApplyWebUpdateResult> {
    throw new UpdaterError("UNSUPPORTED_PLATFORM", "Web runtime is not supported for hybrid updater v1.");
  }

  public async downloadApk(_options: DownloadApkOptions): Promise<DownloadResult> {
    throw new UpdaterError("UNSUPPORTED_PLATFORM", "Web runtime is not supported for hybrid updater v1.");
  }

  public async installApk(_options: InstallApkOptions): Promise<InstallApkResult> {
    throw new UpdaterError("UNSUPPORTED_PLATFORM", "Web runtime is not supported for hybrid updater v1.");
  }

  public async resetToBuiltin(): Promise<ResetResult> {
    throw new UpdaterError("UNSUPPORTED_PLATFORM", "Web runtime is not supported for hybrid updater v1.");
  }

  public addListener(
    eventName: "onUpdateAvailable",
    listenerFunc: (event: UpdateCheckResult) => void
  ): Promise<PluginListenerHandle>;
  public addListener(
    eventName: "onDownloadProgress",
    listenerFunc: (event: { artifactType: "apk" | "web_bundle"; progress: number }) => void
  ): Promise<PluginListenerHandle>;
  public addListener(
    eventName: "onInstallRequired",
    listenerFunc: (event: { reason: "apk_required" | "incompatible_native" }) => void
  ): Promise<PluginListenerHandle>;
  public addListener(
    eventName: "onError",
    listenerFunc: (event: { code: string; message: string }) => void
  ): Promise<PluginListenerHandle>;
  public async addListener(
    _eventName: "onUpdateAvailable" | "onDownloadProgress" | "onInstallRequired" | "onError",
    _listenerFunc: (...args: never[]) => void
  ): Promise<PluginListenerHandle> {
    throw new UpdaterError("UNSUPPORTED_PLATFORM", "Web runtime is not supported for hybrid updater v1.");
  }
}

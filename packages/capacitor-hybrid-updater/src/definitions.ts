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
import type { PluginListenerHandle } from "@capacitor/core";

export interface HybridUpdaterPlugin {
  configure(options: UpdaterConfig): Promise<void>;
  getCurrentState(): Promise<UpdaterState>;
  checkForUpdate(options: CheckForUpdateOptions): Promise<UpdateCheckResult>;
  downloadUpdate(options: DownloadUpdateOptions): Promise<DownloadResult>;
  applyWebUpdate(options: ApplyWebUpdateOptions): Promise<ApplyWebUpdateResult>;
  downloadApk(options: DownloadApkOptions): Promise<DownloadResult>;
  installApk(options: InstallApkOptions): Promise<InstallApkResult>;
  resetToBuiltin(): Promise<ResetResult>;
  addListener(eventName: "onUpdateAvailable", listenerFunc: (event: UpdateCheckResult) => void): Promise<PluginListenerHandle>;
  addListener(
    eventName: "onDownloadProgress",
    listenerFunc: (event: { artifactType: "apk" | "web_bundle"; progress: number }) => void
  ): Promise<PluginListenerHandle>;
  addListener(
    eventName: "onInstallRequired",
    listenerFunc: (event: { reason: "apk_required" | "incompatible_native" }) => void
  ): Promise<PluginListenerHandle>;
  addListener(eventName: "onError", listenerFunc: (event: { code: string; message: string }) => void): Promise<PluginListenerHandle>;
}

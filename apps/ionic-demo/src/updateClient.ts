import { HybridUpdater, type UpdateCheckResult } from "@hybrid/capacitor-hybrid-updater";

/** Use host machine from Android emulator: `http://10.0.2.2:8080` */
const defaultBase = import.meta.env.VITE_LARAVEL_BASE_URL ?? "http://127.0.0.1:8080";

export async function initUpdater(): Promise<void> {
  await HybridUpdater.configure({
    provider: "laravel",
    channel: "stable",
    mode: "hybrid",
    providerOptions: {
      baseUrl: defaultBase
    }
  });
}

export async function checkForUpdates(): Promise<UpdateCheckResult> {
  return HybridUpdater.checkForUpdate({
    mode: "hybrid",
    channel: "stable"
  });
}

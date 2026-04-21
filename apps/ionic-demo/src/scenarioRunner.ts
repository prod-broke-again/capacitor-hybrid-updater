import type { ArtifactType } from "@hybrid/capacitor-hybrid-updater";
import { checkForUpdates, initUpdater } from "./updateClient";

export type DemoScenario = "no-update" | "web-update" | "apk-update" | "incompatible-native";

export async function runScenario(name: DemoScenario): Promise<void> {
  await initUpdater();
  const result = await checkForUpdates();

  switch (name) {
    case "no-update":
      if (result.hasUpdate) {
        throw new Error("Expected no update.");
      }
      return;
    case "web-update":
      if (!result.hasUpdate || result.mode === "apk") {
        throw new Error("Expected web-capable update.");
      }
      return;
    case "apk-update":
      if (!result.manifest || !result.manifest.artifacts.some((artifact: { type: ArtifactType }) => artifact.type === "apk")) {
        throw new Error("Expected APK artifact.");
      }
      return;
    case "incompatible-native":
      if (result.compatibility.isCompatible) {
        throw new Error("Expected incompatible native state.");
      }
      return;
    default:
      throw new Error(`Unknown scenario: ${name}`);
  }
}

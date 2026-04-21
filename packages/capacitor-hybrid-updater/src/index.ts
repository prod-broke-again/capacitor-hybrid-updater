import { registerPlugin } from "@capacitor/core";
import type { HybridUpdaterPlugin } from "./definitions";

const HybridUpdater = registerPlugin<HybridUpdaterPlugin>("HybridUpdater", {
  web: () => import("./web").then((module) => new module.HybridUpdaterWeb())
});

export * from "./contracts";
export * from "./client";
export * from "./core";
export * from "./definitions";
export * from "./errors";
export * from "./manifest";
export * from "./provider";
export * from "./providerRouter";
export * from "./providers/githubReleasesProvider";
export * from "./providers/laravelProvider";
export { HybridUpdater };

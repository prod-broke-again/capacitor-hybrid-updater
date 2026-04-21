import { HybridUpdater } from "./index";
import { UpdaterCore } from "./core";
import { ProviderRouter } from "./providerRouter";
import { LaravelProvider } from "./providers/laravelProvider";
import { GitHubReleasesProvider } from "./providers/githubReleasesProvider";
import { UpdaterError } from "./errors";
import type { ArtifactType, UpdaterConfig } from "./contracts";

export function createUpdaterCore(config: UpdaterConfig): UpdaterCore {
  const router = new ProviderRouter();
  const providerOptions = config.providerOptions;

  if (config.provider === "laravel") {
    const baseUrl = providerOptions.baseUrl;
    if (typeof baseUrl !== "string" || baseUrl.length === 0) {
      throw new UpdaterError("CONFIG_INVALID", "Laravel provider requires providerOptions.baseUrl.");
    }
    const routePrefix =
      typeof providerOptions.routePrefix === "string" && providerOptions.routePrefix.length > 0
        ? providerOptions.routePrefix
        : undefined;
    router.register(
      new LaravelProvider({
        baseUrl,
        token: typeof providerOptions.token === "string" ? providerOptions.token : undefined,
        timeoutMs: typeof config.requestTimeoutMs === "number" ? config.requestTimeoutMs : undefined,
        routePrefix
      })
    );
  } else {
    const owner = providerOptions.owner;
    const repo = providerOptions.repo;
    if (typeof owner !== "string" || typeof repo !== "string") {
      throw new UpdaterError("CONFIG_INVALID", "GitHub provider requires providerOptions.owner and providerOptions.repo.");
    }
    router.register(
      new GitHubReleasesProvider({
        owner,
        repo,
        token: typeof providerOptions.token === "string" ? providerOptions.token : undefined,
        timeoutMs: typeof config.requestTimeoutMs === "number" ? config.requestTimeoutMs : undefined
      })
    );
  }

  return new UpdaterCore(router, {
    getCurrentState: () => HybridUpdater.getCurrentState(),
    downloadArtifact: (url: string, artifactType: ArtifactType, checksum?: string) =>
      HybridUpdater.downloadUpdate({
        artifactType,
        manifest: {
          version: "0.0.0",
          releaseDate: new Date().toISOString(),
          channel: config.channel,
          updateType: "apk_or_ota",
          minNativeVersion: "0.0.0",
          minNativeBuild: 0,
          artifacts: [{ type: artifactType, url, checksum: checksum ?? "" }]
        }
      }),
    applyWebBundle: (options) => HybridUpdater.applyWebUpdate(options),
    installApk: (options) => HybridUpdater.installApk(options),
    resetToBuiltin: () => HybridUpdater.resetToBuiltin()
  });
}

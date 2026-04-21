import type { NormalizedManifest, UpdateProvider } from "../contracts";
import { UpdaterError } from "../errors";
import { httpGetJson } from "../http";
import { validateManifest } from "../manifest";
import type { ProviderCheckContext, ProviderResult, UpdateProviderContract } from "../provider";

interface GitHubReleasesProviderOptions {
  owner: string;
  repo: string;
  token?: string;
  timeoutMs?: number;
}

interface GitHubRelease {
  tag_name: string;
  published_at: string;
  body?: string;
  assets: Array<{
    name: string;
    browser_download_url: string;
  }>;
}

export class GitHubReleasesProvider implements UpdateProviderContract {
  public readonly id: UpdateProvider = "githubReleases";

  public constructor(private readonly options: GitHubReleasesProviderOptions) {}

  public async checkLatest(_context: ProviderCheckContext): Promise<ProviderResult> {
    const release = await this.loadLatestRelease();
    const manifestAsset = release.assets.find((asset) => asset.name === "manifest.json");
    if (!manifestAsset) {
      throw new UpdaterError("MANIFEST_INVALID", "GitHub release does not include manifest.json.", {
        provider: this.id,
        context: { tag: release.tag_name }
      });
    }

    const manifest = await httpGetJson<NormalizedManifest>(manifestAsset.browser_download_url, {
      timeoutMs: this.options.timeoutMs,
      headers: this.options.token ? { Authorization: `Bearer ${this.options.token}` } : undefined
    });

    return {
      provider: this.id,
      manifest: validateManifest(manifest)
    };
  }

  private async loadLatestRelease(): Promise<GitHubRelease> {
    const url = `https://api.github.com/repos/${this.options.owner}/${this.options.repo}/releases/latest`;
    return httpGetJson<GitHubRelease>(url, {
      timeoutMs: this.options.timeoutMs,
      headers: {
        Accept: "application/vnd.github+json",
        ...(this.options.token ? { Authorization: `Bearer ${this.options.token}` } : {})
      }
    });
  }
}

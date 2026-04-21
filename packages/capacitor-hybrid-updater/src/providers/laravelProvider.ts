import type { NormalizedManifest, UpdateProvider } from "../contracts";
import { validateManifest } from "../manifest";
import { httpGetJson } from "../http";
import type { ProviderCheckContext, ProviderResult, UpdateProviderContract } from "../provider";

interface LaravelProviderOptions {
  baseUrl: string;
  token?: string;
  timeoutMs?: number;
  /**
   * Path segment after base URL, no leading slash (e.g. `api/updater`).
   * Must match `config('hybrid-updater.route_prefix')` on the server.
   * @default "api/updater"
   */
  routePrefix?: string;
}

/** Response from Laravel GET `{routePrefix}/check` */
interface LaravelCheckResponse {
  meta: { channel: string; checkedAt: string; source: string };
  current: {
    nativeVersion: string | null;
    nativeBuild: number | null;
    webVersion: string | null;
  };
  decision: {
    hasUpdate: boolean;
    updateType: string | null;
    isCompatible: boolean;
    reason: string | null;
  };
  manifest: unknown | null;
}

function joinBaseRoute(baseUrl: string, routePrefix: string): string {
  const base = baseUrl.replace(/\/$/, "");
  const rp = routePrefix.replace(/^\//, "").replace(/\/$/, "");
  return `${base}/${rp}`;
}

export class LaravelProvider implements UpdateProviderContract {
  public readonly id: UpdateProvider = "laravel";

  public constructor(private readonly options: LaravelProviderOptions) {}

  public async checkLatest(context: ProviderCheckContext): Promise<ProviderResult> {
    const query = new URLSearchParams({
      channel: context.channel,
      platform: "android",
      native_version: context.current.currentNativeVersion,
      native_build: String(context.current.currentNativeBuild),
      web_version: context.current.currentWebBundleVersion
    });
    const routePrefix = this.options.routePrefix ?? "api/updater";
    const url = `${joinBaseRoute(this.options.baseUrl, routePrefix)}/check?${query.toString()}`;

    const payload = await httpGetJson<LaravelCheckResponse>(url, {
      timeoutMs: this.options.timeoutMs,
      headers: this.options.token ? { Authorization: `Bearer ${this.options.token}` } : undefined
    });

    if (payload.manifest === null || payload.manifest === undefined) {
      return {
        provider: this.id,
        serverDecision: {
          hasUpdate: payload.decision.hasUpdate,
          isCompatible: payload.decision.isCompatible,
          reason: payload.decision.reason === "incompatible_native" ? "incompatible_native" : undefined
        }
      };
    }

    const manifest = validateManifest(this.normalizeManifestKeys(payload.manifest));

    return {
      provider: this.id,
      manifest,
      serverDecision: {
        hasUpdate: payload.decision.hasUpdate,
        isCompatible: payload.decision.isCompatible,
        reason: payload.decision.reason === "incompatible_native" ? "incompatible_native" : undefined
      }
    };
  }

  /** Accept camelCase or snake_case manifest keys from JSON. */
  private normalizeManifestKeys(raw: unknown): unknown {
    if (raw === null || typeof raw !== "object") {
      return raw;
    }
    const o = raw as Record<string, unknown>;
    const out: Record<string, unknown> = { ...o };
    if (out.min_native_version !== undefined && out.minNativeVersion === undefined) {
      out.minNativeVersion = out.min_native_version;
    }
    if (out.min_native_build !== undefined && out.minNativeBuild === undefined) {
      out.minNativeBuild = out.min_native_build;
    }
    if (out.release_date !== undefined && out.releaseDate === undefined) {
      out.releaseDate = out.release_date;
    }
    return out;
  }
}

import type { NormalizedManifest, UpdaterState, UpdateMode, UpdateProvider } from "./contracts";

export interface ProviderCheckContext {
  mode: UpdateMode;
  channel: string;
  current: UpdaterState;
}

export interface ProviderResult {
  provider: UpdateProvider;
  manifest?: NormalizedManifest;
  /** When backend already computed decision (e.g. Laravel GET `{route_prefix}/check`). */
  serverDecision?: {
    hasUpdate: boolean;
    isCompatible: boolean;
    reason?: "incompatible_native";
  };
}

export interface UpdateProviderContract {
  readonly id: UpdateProvider;
  checkLatest(context: ProviderCheckContext): Promise<ProviderResult>;
}

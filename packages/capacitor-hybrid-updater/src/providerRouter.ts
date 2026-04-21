import { UpdaterError } from "./errors";
import type { UpdateProvider } from "./contracts";
import type { UpdateProviderContract } from "./provider";

export class ProviderRouter {
  private readonly providers = new Map<UpdateProvider, UpdateProviderContract>();

  public register(provider: UpdateProviderContract): void {
    this.providers.set(provider.id, provider);
  }

  public resolve(id: UpdateProvider): UpdateProviderContract {
    const provider = this.providers.get(id);
    if (!provider) {
      throw new UpdaterError("PROVIDER_UNAVAILABLE", `Provider "${id}" is not registered.`, {
        provider: id
      });
    }
    return provider;
  }
}

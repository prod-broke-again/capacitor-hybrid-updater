import { UpdaterError } from "./errors";

export interface HttpClientOptions {
  timeoutMs?: number;
  headers?: Record<string, string>;
}

export async function httpGetJson<T>(url: string, options: HttpClientOptions = {}): Promise<T> {
  const controller = new AbortController();
  const timeoutMs = options.timeoutMs ?? 15000;
  const timeoutId = setTimeout(() => controller.abort(), timeoutMs);

  try {
    const response = await fetch(url, {
      method: "GET",
      headers: options.headers,
      signal: controller.signal
    });
    if (!response.ok) {
      throw new UpdaterError("NETWORK_ERROR", `Request failed with status ${response.status}.`, {
        context: { url, status: response.status }
      });
    }
    return (await response.json()) as T;
  } catch (error) {
    if (error instanceof UpdaterError) {
      throw error;
    }
    throw new UpdaterError("NETWORK_ERROR", "Failed to load JSON payload.", {
      context: { url },
      cause: error
    });
  } finally {
    clearTimeout(timeoutId);
  }
}

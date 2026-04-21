export type UpdaterErrorCode =
  | "CONFIG_INVALID"
  | "PROVIDER_UNAVAILABLE"
  | "MANIFEST_INVALID"
  | "NETWORK_ERROR"
  | "CHECKSUM_MISMATCH"
  | "INCOMPATIBLE_NATIVE"
  | "APK_INSTALL_FAILED"
  | "WEB_APPLY_FAILED"
  | "RUNTIME_IO_ERROR"
  | "UNSUPPORTED_PLATFORM";

export interface UpdaterErrorContext {
  provider?: string;
  context?: Record<string, unknown>;
  cause?: unknown;
}

export class UpdaterError extends Error {
  public readonly code: UpdaterErrorCode;
  public readonly provider?: string;
  public readonly context?: Record<string, unknown>;
  public readonly cause?: unknown;

  public constructor(code: UpdaterErrorCode, message: string, details: UpdaterErrorContext = {}) {
    super(message);
    this.name = "UpdaterError";
    this.code = code;
    this.provider = details.provider;
    this.context = details.context;
    this.cause = details.cause;
  }
}

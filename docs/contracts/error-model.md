# Error Model (v1)

All plugin failures are normalized into:

```ts
type UpdaterErrorCode =
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
```

```ts
interface UpdaterError {
  code: UpdaterErrorCode;
  message: string;
  cause?: unknown;
  provider?: string;
  context?: Record<string, unknown>;
}
```

Provider errors and Android exceptions must be mapped to this shape before emitting `onError` or rejecting calls.

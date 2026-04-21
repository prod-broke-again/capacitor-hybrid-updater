import { type NormalizedManifest, type ManifestArtifact } from "./contracts";
import { UpdaterError } from "./errors";

function isArtifact(value: unknown): value is ManifestArtifact {
  if (typeof value !== "object" || value === null) {
    return false;
  }

  const candidate = value as Partial<ManifestArtifact>;
  return typeof candidate.type === "string" && typeof candidate.url === "string" && typeof candidate.checksum === "string";
}

export function validateManifest(value: unknown): NormalizedManifest {
  if (typeof value !== "object" || value === null) {
    throw new UpdaterError("MANIFEST_INVALID", "Manifest must be an object.");
  }

  const candidate = value as Partial<NormalizedManifest>;
  if (!candidate.version || !candidate.releaseDate || !candidate.channel || !candidate.updateType) {
    throw new UpdaterError("MANIFEST_INVALID", "Manifest missing required base fields.");
  }

  if (!Array.isArray(candidate.artifacts) || candidate.artifacts.length === 0 || !candidate.artifacts.every(isArtifact)) {
    throw new UpdaterError("MANIFEST_INVALID", "Manifest artifacts are invalid.");
  }

  return {
    version: candidate.version,
    releaseDate: candidate.releaseDate,
    channel: candidate.channel,
    updateType: candidate.updateType,
    minNativeVersion: candidate.minNativeVersion ?? "0.0.0",
    minNativeBuild: candidate.minNativeBuild ?? 0,
    notes: candidate.notes,
    artifacts: candidate.artifacts,
    meta: candidate.meta
  };
}

export function getArtifactByType(manifest: NormalizedManifest, type: ManifestArtifact["type"]): ManifestArtifact | undefined {
  return manifest.artifacts.find((artifact) => artifact.type === type);
}

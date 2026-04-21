export function compareVersions(a: string, b: string): number {
  const pa = a.split(".").map((part) => Number.parseInt(part, 10) || 0);
  const pb = b.split(".").map((part) => Number.parseInt(part, 10) || 0);

  for (let index = 0; index < Math.max(pa.length, pb.length); index += 1) {
    const left = pa[index] ?? 0;
    const right = pb[index] ?? 0;
    if (left > right) return 1;
    if (left < right) return -1;
  }

  return 0;
}

export function isServerNewer(
  serverVersion: string,
  serverBuild: number,
  currentVersion: string,
  currentBuild: number
): boolean {
  const versionCmp = compareVersions(serverVersion, currentVersion);
  if (versionCmp > 0) return true;
  if (versionCmp < 0) return false;
  return serverBuild > currentBuild;
}

<?php

declare(strict_types=1);

namespace HybridUpdater\Domain\Service;

use HybridUpdater\Domain\Dto\ClientVersionsSnapshot;
use HybridUpdater\Domain\Dto\NormalizedManifestDto;
use HybridUpdater\Domain\Enumeration\IncompatibilityReason;

final readonly class UpdateDecisionResult
{
    public function __construct(
        public bool $hasUpdate,
        public bool $isCompatible,
        public ?IncompatibilityReason $reason = null,
    ) {}
}

/**
 * Decides hasUpdate / compatibility when client sends optional version snapshot.
 */
final class UpdateDecisionEngine
{
    public function __construct(
        private SemverComparator $semver = new SemverComparator(),
    ) {}

    public function decide(
        ?NormalizedManifestDto $manifest,
        ClientVersionsSnapshot $client,
    ): UpdateDecisionResult {
        if ($manifest === null) {
            return new UpdateDecisionResult(hasUpdate: false, isCompatible: true);
        }

        $compat = $this->evaluateCompatibility($manifest, $client);
        if (! $compat->isCompatible) {
            return new UpdateDecisionResult(
                hasUpdate: false,
                isCompatible: false,
                reason: $compat->reason,
            );
        }

        $hasUpdate = $this->evaluateHasNewerWebBundle($manifest, $client);

        return new UpdateDecisionResult(
            hasUpdate: $hasUpdate,
            isCompatible: true,
        );
    }

    private function evaluateCompatibility(
        NormalizedManifestDto $manifest,
        ClientVersionsSnapshot $client,
    ): UpdateDecisionResult {
        if ($client->nativeVersion === null && $client->nativeBuild === null) {
            return new UpdateDecisionResult(hasUpdate: false, isCompatible: true);
        }

        $versionOk = $client->nativeVersion === null
            || $this->semver->isGreaterOrEqual($client->nativeVersion, $manifest->minNativeVersion);

        $buildOk = $client->nativeBuild === null
            || $client->nativeBuild >= $manifest->minNativeBuild;

        if ($versionOk && $buildOk) {
            return new UpdateDecisionResult(hasUpdate: false, isCompatible: true);
        }

        return new UpdateDecisionResult(
            hasUpdate: false,
            isCompatible: false,
            reason: IncompatibilityReason::IncompatibleNative,
        );
    }

    private function evaluateHasNewerWebBundle(
        NormalizedManifestDto $manifest,
        ClientVersionsSnapshot $client,
    ): bool {
        if ($client->webVersion === null || $client->webVersion === '') {
            return true;
        }

        return $this->semver->isGreater($manifest->version, $client->webVersion);
    }
}

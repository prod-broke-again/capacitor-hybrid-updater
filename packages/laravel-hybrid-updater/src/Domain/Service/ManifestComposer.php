<?php

declare(strict_types=1);

namespace HybridUpdater\Domain\Service;

use HybridUpdater\Domain\Dto\AndroidReleaseSnapshot;
use HybridUpdater\Domain\Dto\ArtifactDto;
use HybridUpdater\Domain\Dto\NormalizedManifestDto;
use HybridUpdater\Domain\Dto\WebBundleSnapshot;
use HybridUpdater\Domain\Enumeration\UpdateType;

final class ManifestComposer
{
    public function compose(
        ?WebBundleSnapshot $web,
        ?AndroidReleaseSnapshot $native,
        string $channel,
    ): ?NormalizedManifestDto {
        if ($web === null && $native === null) {
            return null;
        }

        $artifacts = [];
        if ($web !== null && $web->zipUrl !== '') {
            $artifacts[] = new ArtifactDto(
                type: 'web_bundle',
                url: $web->zipUrl,
                checksum: (string) ($web->checksum ?? ''),
            );
        }

        if ($native !== null && $native->downloadUrl !== '') {
            $artifacts[] = new ArtifactDto(
                type: 'apk',
                url: $native->downloadUrl,
                checksum: (string) ($native->checksum ?? ''),
            );
        }

        if ($artifacts === []) {
            return null;
        }

        $updateType = $this->resolveUpdateType($web !== null, $native !== null);
        $version = $native !== null ? $native->version : $web->version;
        $releaseDate = $native !== null ? $native->releaseDateIso : $web->releaseDateIso;
        $minNativeVersion = $web !== null ? $web->minNativeVersion : '0.0.0';
        $minNativeBuild = $web !== null ? $web->minNativeBuild : 0;
        $notes = $native !== null ? (string) ($native->releaseNotes ?? '') : '';

        return new NormalizedManifestDto(
            version: $version,
            releaseDate: $releaseDate,
            channel: $channel,
            updateType: $updateType,
            minNativeVersion: $minNativeVersion,
            minNativeBuild: $minNativeBuild,
            notes: $notes,
            artifacts: $artifacts,
        );
    }

    private function resolveUpdateType(bool $hasWeb, bool $hasNative): UpdateType
    {
        if ($hasNative && $hasWeb) {
            return UpdateType::ApkOrOta;
        }
        if ($hasNative) {
            return UpdateType::ApkRequired;
        }

        return UpdateType::OtaWebOnly;
    }
}

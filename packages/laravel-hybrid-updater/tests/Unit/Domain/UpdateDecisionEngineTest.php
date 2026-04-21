<?php

declare(strict_types=1);

namespace HybridUpdater\Tests\Unit\Domain;

use HybridUpdater\Domain\Dto\ArtifactDto;
use HybridUpdater\Domain\Dto\ClientVersionsSnapshot;
use HybridUpdater\Domain\Dto\NormalizedManifestDto;
use HybridUpdater\Domain\Enumeration\IncompatibilityReason;
use HybridUpdater\Domain\Enumeration\UpdateType;
use HybridUpdater\Domain\Service\UpdateDecisionEngine;
use PHPUnit\Framework\TestCase;

final class UpdateDecisionEngineTest extends TestCase
{
    public function testIncompatibleNativeBuild(): void
    {
        $manifest = new NormalizedManifestDto(
            version: '2.0.0',
            releaseDate: '2026-01-01T00:00:00Z',
            channel: 'stable',
            updateType: UpdateType::OtaWebOnly,
            minNativeVersion: '1.0.0',
            minNativeBuild: 100,
            notes: '',
            artifacts: [
                new ArtifactDto('web_bundle', 'https://x/z.zip', ''),
            ],
        );

        $engine = new UpdateDecisionEngine();
        $result = $engine->decide(
            $manifest,
            ClientVersionsSnapshot::fromNullable('1.0.0', 1, '1.0.0'),
        );

        self::assertFalse($result->isCompatible);
        self::assertSame(IncompatibilityReason::IncompatibleNative, $result->reason);
    }
}

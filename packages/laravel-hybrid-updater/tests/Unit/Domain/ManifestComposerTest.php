<?php

declare(strict_types=1);

namespace HybridUpdater\Tests\Unit\Domain;

use HybridUpdater\Domain\Dto\AndroidReleaseSnapshot;
use HybridUpdater\Domain\Dto\WebBundleSnapshot;
use HybridUpdater\Domain\Enumeration\UpdateType;
use HybridUpdater\Domain\Service\ManifestComposer;
use PHPUnit\Framework\TestCase;

final class ManifestComposerTest extends TestCase
{
    public function testWebOnly(): void
    {
        $web = new WebBundleSnapshot(
            version: '1.0.0',
            zipUrl: 'https://example.com/b.zip',
            minNativeVersion: '1.0.0',
            minNativeBuild: 1,
            forceReload: false,
            checksum: 'abc',
            channel: 'stable',
            releaseDateIso: '2026-01-01T00:00:00Z',
        );
        $m = new ManifestComposer();
        $manifest = $m->compose($web, null, 'stable');
        self::assertNotNull($manifest);
        self::assertSame(UpdateType::OtaWebOnly, $manifest->updateType);
        self::assertCount(1, $manifest->artifacts);
    }

    public function testApkOnly(): void
    {
        $native = new AndroidReleaseSnapshot(
            version: '2.0.0',
            buildNumber: 10,
            downloadUrl: 'https://example.com/app.apk',
            releaseNotes: null,
            forceUpdate: false,
            checksum: 'def',
            channel: 'stable',
            releaseDateIso: '2026-01-02T00:00:00Z',
        );
        $m = new ManifestComposer();
        $manifest = $m->compose(null, $native, 'stable');
        self::assertNotNull($manifest);
        self::assertSame(UpdateType::ApkRequired, $manifest->updateType);
    }
}

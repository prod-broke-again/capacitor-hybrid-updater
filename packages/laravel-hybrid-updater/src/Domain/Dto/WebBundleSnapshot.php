<?php

declare(strict_types=1);

namespace HybridUpdater\Domain\Dto;

final readonly class WebBundleSnapshot
{
    public function __construct(
        public string $version,
        public string $zipUrl,
        public string $minNativeVersion,
        public int $minNativeBuild,
        public bool $forceReload,
        public ?string $checksum,
        public string $channel,
        public string $releaseDateIso,
    ) {}
}

<?php

declare(strict_types=1);

namespace HybridUpdater\Domain\Dto;

final readonly class AndroidReleaseSnapshot
{
    public function __construct(
        public string $version,
        public int $buildNumber,
        public string $downloadUrl,
        public ?string $releaseNotes,
        public bool $forceUpdate,
        public ?string $checksum,
        public string $channel,
        public string $releaseDateIso,
    ) {}
}

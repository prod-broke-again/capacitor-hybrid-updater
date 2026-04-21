<?php

declare(strict_types=1);

namespace HybridUpdater\Domain\Dto;

final readonly class ArtifactDto
{
    public function __construct(
        public string $type,
        public string $url,
        public string $checksum,
        public ?int $size = null,
    ) {}
}

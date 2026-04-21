<?php

declare(strict_types=1);

namespace HybridUpdater\Domain\Dto;

use HybridUpdater\Domain\Enumeration\UpdateType;

final readonly class NormalizedManifestDto
{
    /**
     * @param list<ArtifactDto> $artifacts
     */
    public function __construct(
        public string $version,
        public string $releaseDate,
        public string $channel,
        public UpdateType $updateType,
        public string $minNativeVersion,
        public int $minNativeBuild,
        public string $notes,
        public array $artifacts,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toApiArray(): array
    {
        return [
            'version' => $this->version,
            'releaseDate' => $this->releaseDate,
            'channel' => $this->channel,
            'updateType' => $this->updateType->value,
            'minNativeVersion' => $this->minNativeVersion,
            'minNativeBuild' => $this->minNativeBuild,
            'notes' => $this->notes,
            'artifacts' => array_map(static fn (ArtifactDto $a): array => [
                'type' => $a->type,
                'url' => $a->url,
                'checksum' => $a->checksum,
                'size' => $a->size,
            ], $this->artifacts),
        ];
    }
}

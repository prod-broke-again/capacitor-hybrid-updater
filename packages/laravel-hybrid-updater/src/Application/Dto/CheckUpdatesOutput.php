<?php

declare(strict_types=1);

namespace HybridUpdater\Application\Dto;

use HybridUpdater\Domain\Dto\NormalizedManifestDto;

final readonly class CheckUpdatesOutput
{
    public function __construct(
        public ResponseMetaDto $meta,
        public CurrentClientEchoDto $current,
        public UpdateDecisionDto $decision,
        public ?NormalizedManifestDto $manifest,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'meta' => [
                'channel' => $this->meta->channel,
                'checkedAt' => $this->meta->checkedAtIso,
                'source' => $this->meta->source,
            ],
            'current' => [
                'nativeVersion' => $this->current->nativeVersion,
                'nativeBuild' => $this->current->nativeBuild,
                'webVersion' => $this->current->webVersion,
            ],
            'decision' => [
                'hasUpdate' => $this->decision->hasUpdate,
                'updateType' => $this->decision->updateType?->value,
                'isCompatible' => $this->decision->isCompatible,
                'reason' => $this->decision->reason?->value,
            ],
            'manifest' => $this->manifest?->toApiArray(),
        ];
    }
}

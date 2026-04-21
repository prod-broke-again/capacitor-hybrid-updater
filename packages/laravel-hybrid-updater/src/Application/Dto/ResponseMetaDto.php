<?php

declare(strict_types=1);

namespace HybridUpdater\Application\Dto;

final readonly class ResponseMetaDto
{
    public function __construct(
        public string $channel,
        public string $checkedAtIso,
        public string $source,
    ) {}
}

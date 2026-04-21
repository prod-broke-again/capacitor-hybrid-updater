<?php

declare(strict_types=1);

namespace HybridUpdater\Application\Dto;

final readonly class CurrentClientEchoDto
{
    public function __construct(
        public ?string $nativeVersion,
        public ?int $nativeBuild,
        public ?string $webVersion,
    ) {}
}

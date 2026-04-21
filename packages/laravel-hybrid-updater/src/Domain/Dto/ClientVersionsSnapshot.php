<?php

declare(strict_types=1);

namespace HybridUpdater\Domain\Dto;

/** Client-reported versions for server-side decision (optional). */
final readonly class ClientVersionsSnapshot
{
    public function __construct(
        public ?string $nativeVersion = null,
        public ?int $nativeBuild = null,
        public ?string $webVersion = null,
    ) {}

    public static function fromNullable(
        ?string $nativeVersion,
        ?int $nativeBuild,
        ?string $webVersion,
    ): self {
        return new self($nativeVersion, $nativeBuild, $webVersion);
    }
}

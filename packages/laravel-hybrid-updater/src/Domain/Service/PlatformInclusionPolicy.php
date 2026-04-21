<?php

declare(strict_types=1);

namespace HybridUpdater\Domain\Service;

final class PlatformInclusionPolicy
{
    public function includeWeb(?string $platform): bool
    {
        return $platform === null || $platform === 'web';
    }

    public function includeNative(?string $platform): bool
    {
        return $platform === null || $platform === 'android';
    }
}

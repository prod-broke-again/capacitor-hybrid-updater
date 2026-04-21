<?php

declare(strict_types=1);

namespace HybridUpdater\Infrastructure\Support;

use Illuminate\Contracts\Cache\Repository;

final class UpdateCacheInvalidator
{
    public function __construct(
        private readonly Repository $cache,
    ) {}

    public function forgetChannel(string $channel): void
    {
        $this->cache->forget('hybrid-updater:v2:web:'.$channel);
        $this->cache->forget('hybrid-updater:v2:native:'.$channel);
    }
}

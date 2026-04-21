<?php

declare(strict_types=1);

namespace HybridUpdater\Infrastructure\Cache;

use HybridUpdater\Application\Port\AndroidReleaseReadRepository;
use HybridUpdater\Domain\Dto\AndroidReleaseSnapshot;
use HybridUpdater\Domain\ValueObject\ChannelName;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

final class CachedAndroidReleaseReadRepository implements AndroidReleaseReadRepository
{
    public function __construct(
        private readonly AndroidReleaseReadRepository $inner,
        private readonly CacheRepository $cache,
        private readonly int $ttlSeconds,
    ) {}

    public function findLatestActive(ChannelName $channel): ?AndroidReleaseSnapshot
    {
        $key = 'hybrid-updater:v2:native:'.$channel->toString();

        return $this->cache->remember($key, $this->ttlSeconds, fn (): ?AndroidReleaseSnapshot => $this->inner->findLatestActive($channel));
    }
}

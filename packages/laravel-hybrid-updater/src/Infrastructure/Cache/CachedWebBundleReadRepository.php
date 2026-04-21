<?php

declare(strict_types=1);

namespace HybridUpdater\Infrastructure\Cache;

use HybridUpdater\Application\Port\WebBundleReadRepository;
use HybridUpdater\Domain\Dto\WebBundleSnapshot;
use HybridUpdater\Domain\ValueObject\ChannelName;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

final class CachedWebBundleReadRepository implements WebBundleReadRepository
{
    public function __construct(
        private readonly WebBundleReadRepository $inner,
        private readonly CacheRepository $cache,
        private readonly int $ttlSeconds,
    ) {}

    public function findLatestActive(ChannelName $channel): ?WebBundleSnapshot
    {
        $key = 'hybrid-updater:v2:web:'.$channel->toString();

        return $this->cache->remember($key, $this->ttlSeconds, fn (): ?WebBundleSnapshot => $this->inner->findLatestActive($channel));
    }
}
